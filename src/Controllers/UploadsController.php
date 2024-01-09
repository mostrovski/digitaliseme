<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\FileNotFoundException;
use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;
use Digitaliseme\Core\Storage\File;
use Digitaliseme\Exceptions\FileException;
use Digitaliseme\Exceptions\UploadedFileException;
use Digitaliseme\Models\File as FileModel;
use Throwable;

class UploadsController extends Controller
{
    public function index(): View
    {
        try {
            $uploads = FileModel::go()->query()
                ->whereNull('document_id')
                ->where('user_id', '=', auth()->id())
                ->get();
            if (count($uploads) === 0) {
                flash()->info('There is nothing to work on, upload new file <a href="https://digitaliseme.ddev.site/uploads/create">here</a>');
            }

            return $this->view('uploads/index', ['uploads' => $uploads]);
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->view('uploads/index');
        }
    }

    public function create(): View
    {
        return $this->view('uploads/create');
    }

    public function store(): Redirect
    {
        if (! $this->isPostRequest()) {
            return $this->redirect('404');
        }

        try {
            $file = File::fromUpload($this->request()->files()['docfile']);
            $this->verify($file);
        } catch (FileNotFoundException) {
            flash()->error('File was not chosen');
            return $this->redirect('uploads/create');
        } catch (UploadedFileException $e) {
            flash()->error($e->getMessage());
            return $this->redirect('uploads/create');
        }

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
                return $this->redirect('uploads');
            } catch (Throwable $e) {
                logger()->error($e->getMessage());
                flash()->error(config('app.messages.error.GENERAL_ERROR'));
                return $this->redirect('uploads/create');
            }
        }

        flash()->error('Failed to save uploaded file.');
        return $this->redirect('uploads/create');
    }

    public function delete($id = null): Redirect
    {
        if (! isset($id)) {
            return $this->redirect('404');
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
            return $this->redirect('uploads');
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (FileException $e) {
            flash()->error($e->getMessage());
            return $this->redirect('uploads');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('uploads');
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
}
