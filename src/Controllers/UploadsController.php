<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\FileNotFoundException;
use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\Storage\File;
use Digitaliseme\Exceptions\FileException;
use Digitaliseme\Exceptions\UploadedFileException;
use Digitaliseme\Models\File as FileModel;
use Throwable;

class UploadsController extends Controller
{
    protected array $data;

    public function __construct()
    {
        $this->setData();
    }

    public function index(): void
    {
        try {
            $files = FileModel::go()->query()
                ->whereNull('document_id')
                ->where('user_id', '=', auth()->id())
                ->get();
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            $this->view('uploads/index', $this->data);
        }

        $uploads = $files ?? [];

        if (count($uploads) === 0) {
            flash()->info('There is nothing to work on, upload new file <a href="https://digitaliseme.ddev.site/uploads/create">here</a>');
        }

        $this->data['uploads'] = $uploads;

        $this->view('uploads/index', $this->data);
    }

    public function create(): void
    {
        $this->data['title'] = config('app.page.titles')['uploads/create'];
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->view('uploads/create', $this->data);
    }

    public function store(): void
    {
        if (! $this->isPostRequest() ||
            ! $this->isValidToken($_POST['token'])
        ) {
            $this->redirect('404');
        }

        $this->destroyToken();

        try {
            $file = File::fromUpload($_FILES['docfile']);
            $this->verify($file);
        } catch (FileNotFoundException) {
            flash()->error('File was not chosen');
            $this->redirect('uploads/create');
        } catch (UploadedFileException $e) {
            flash()->error($e->getMessage());
            $this->redirect('uploads/create');
        }

        if (isset($file)) {
            $extension = empty($file->extension()) ? '' : '.'.$file->extension();
            $relativePath = randomString().$extension;

            if ($file->moveTo(documents_path($relativePath))) {
                try {
                    FileModel::go()->create([
                        'filename' => $file->name(),
                        'path' => $relativePath,
                        'user_id' => auth()->id(),
                    ]);
                    flash()->success('File was successfully uploaded');
                    $this->redirect('uploads');
                } catch (Throwable $e) {
                    logger()->error($e->getMessage());
                    flash()->error(config('app.messages.error.GENERAL_ERROR'));
                    $this->redirect('uploads/create');
                }
            } else {
                flash()->error('Failed to save uploaded file.');
                $this->redirect('uploads/create');
            }
        }
    }

    public function delete($id = null): void
    {
        if (! isset($id)) {
            $this->redirect('404');
        }

        try {
            /** @var FileModel $file */
            $file = FileModel::go()->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', auth()->id())
                ->firstOrFail();

            if (! File::fromExisting($file->fullPath())->delete()) {
                throw FileException::delete();
            }

            if (! $file->delete()) {
                throw FileException::deleteRecord();
            }

            flash()->success('File was successfully deleted');
            $this->redirect('uploads');
        } catch (RecordNotFoundException) {
            $this->redirect('404');
        } catch (FileException $e) {
            flash()->error($e->getMessage());
            $this->redirect('uploads');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            $this->redirect('uploads');
        }
    }

    /**
     * @throws UploadedFileException
     */
    protected function verify(File $file): void
    {
        $info = $file->getInfo();

        if (! $info->isFile()) {
            throw UploadedFileException::invalid();
        }

        if ((int) $info->getSize() === 0) {
            throw UploadedFileException::empty();
        }

        if ((int) $info->getSize() > config('app.files.max_size')) {
            throw UploadedFileException::size();
        }

        if (! in_array($file->mimeType(), config('app.files.supported_types'), true)) {
            throw UploadedFileException::type();
        }
    }

    protected function setData(): void
    {
        $this->data = [
            'title' => config('app.page.titles')['uploads'],
            'uploads' => [],
        ];
    }
}
