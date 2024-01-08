<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\FileNotFoundException;
use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Http\Responses\Download;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;
use Digitaliseme\Core\Storage\File as FileObject;
use Digitaliseme\DataEntities\Keywords;
use Digitaliseme\Enumerations\DocumentType;
use Digitaliseme\Exceptions\KeywordException;
use Digitaliseme\Models\Document;
use Digitaliseme\Models\File;
use Digitaliseme\Models\Issuer;
use Digitaliseme\Models\Keyword;
use Digitaliseme\Models\StoragePlace;
use Throwable;

class DocumentsController extends Controller
{
    public function index(): View
    {
        try {
            $documents = Document::go()->query()->get();
            if (count($documents) === 0) {
                flash()->info('The archive is empty, upload new file <a href="https://digitaliseme.ddev.site/uploads/create">here</a>');
            }
            return $this->view('documents/index', ['documents' => $documents]);
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->view('documents/index');
        }
    }

    public function create($id = null): Redirect|View
    {
        if (! isset($id)) {
            return $this->redirect('404');
        }

        try {
            /** @var File $file */
            $file = File::go()->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', auth()->id())
                ->whereNull('document_id')
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->view('documents/create');
        }

        $_SESSION['upfile'] = $file->id;

        return $this->view('documents/create', ['filename' => $file->filename]);
    }

    /**
     * @throws ValidatorException
     */
    public function store(): Redirect
    {
        if (! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            return $this->redirect('404');
        }

        $fileId = $_SESSION['upfile'] ?? null;

        if (empty($fileId) || $fileId !== (int) $_POST['fileId']) {
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('uploads');
        }

        unset($_SESSION['upfile']);

        $validator = $this->validate($_POST, $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return $this->withErrors($validator->getErrors())->redirect('documents/create/'.$fileId);
        }

        $values = $validator->getValidated();

        if (! empty($values['keywords'])) {
            try {
                $keywords = Keywords::fromString($values['keywords'])->all();
            } catch (KeywordException $e) {
                return $this->withErrors(['keywords' => [$e->getMessage()]])->redirect('documents/create/'.$fileId);
            }
        }

        try {
            $issuer = Issuer::go()->firstOrCreate([
                'name' => $values['issuer_name'],
                'email' => $values['issuer_email'],
                'phone' => $values['issuer_phone'],
            ], uniqueKey: 'name');

            $storage = StoragePlace::go()->firstOrCreate([
                'place' => $values['storage'],
            ]);

            $document = Document::go()->create([
                'title' => $values['title'],
                'type' => $values['type'],
                'issue_date' => $values['issue_date'],
                'issuer_id' => $issuer->id,
                'storage_id' => $storage->id,
                'user_id' => auth()->id(),
            ]);

            File::go()->query()
                ->where('id', '=', $fileId)
                ->update([
                    'filename' => $values['filename'],
                    'document_id' => $document->id,
                ]);

            if (isset($keywords)) {
                $pivot = $document->pivot('document_keywords');

                foreach ($keywords as $word) {
                    $keyword = Keyword::go()->firstOrCreate(['word' => $word]);
                    $pivot->create([
                        'document_id' => $document->id,
                        'keyword_id' => $keyword->id,
                    ]);
                }
            }
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('documents/create/'.$fileId);
        }

        flash()->success('Document was successfully saved');
        return $this->redirect('documents');
    }

    public function show($id = null): Redirect|View
    {
        if (! isset($id)) {
            return $this->redirect('404');
        }

        try {
            $document = Document::go()->findOrFail($id);

            return $this->view('documents/show', [
                'document' => $document,
                'filename' => $document->file()?->filename,
                'issuer' => $document->issuer(),
                'storage' => $document->storage()?->place,
                'keywords' => Keywords::fromModelArray($document->keywords())->toString(),
            ]);
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->view('documents/show');
        }
    }

    public function edit($id = null): Redirect|View
    {
        if (! isset($id)) {
            return $this->redirect('404');
        }

        try {
            /** @var Document $document */
            $document = Document::go()->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', auth()->id())
                ->firstOrFail();

            return $this->view('documents/edit', [
                'document' => $document,
                'filename' => $document->file()?->filename,
                'issuer' => $document->issuer(),
                'storage' => $document->storage()?->place,
                'keywords' => Keywords::fromModelArray($document->keywords())->toString(),
            ]);
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->view('documents/edit');
        }
    }

    /**
     * @throws ValidatorException
     */
    public function update($id = null): Redirect
    {
        if (! isset($id) ||
            ! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            return $this->redirect('404');
        }

        $validator = $this->validate($_POST, $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return $this->withErrors($validator->getErrors())->redirect('documents/edit/'.$id);
        }

        $values = $validator->getValidated();

        try {
            $keywords = Keywords::fromString((string) $values['keywords'])->all();
        } catch (KeywordException $e) {
            return $this->withErrors(['keywords' => [$e->getMessage()]])->redirect('documents/edit/'.$id);
        }

        try {
            /** @var Document $document */
            $document = Document::go()->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', auth()->id())
                ->firstOrFail();

            $document->file()?->update([
                'filename' => $values['filename'],
            ]);

            $issuer = Issuer::go()->updateOrCreate([
                'name' => $values['issuer_name'],
                'email' => $values['issuer_email'],
                'phone' => $values['issuer_phone'],
            ], uniqueKey: 'name');

            $storage = StoragePlace::go()->firstOrCreate([
                'place' => $values['storage'],
            ]);

            $document->update([
                'title' => $values['title'],
                'type' => $values['type'],
                'issue_date' => $values['issue_date'],
                'issuer_id' => $issuer->id,
                'storage_id' => $storage->id,
            ]);

            if (isset($keywords)) {
                $pivot = $document->pivot('document_keywords');
                $pivot->where('document_id', '=', $document->id)->delete();

                foreach ($keywords as $word) {
                    $keyword = Keyword::go()->firstOrCreate(['word' => $word]);
                    $pivot->create([
                        'document_id' => $document->id,
                        'keyword_id' => $keyword->id,
                    ]);
                }
            }
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('documents/edit/'.$id);
        }

        flash()->success('Document updated successfully');
        return $this->redirect('documents');
    }

    public function delete($id = null): Redirect
    {
        if (! isset($id) ||
            ! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            return $this->redirect('404');
        }

        try {
            /** @var Document $document */
            $document = Document::go()->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', auth()->id())
                ->firstOrFail();

            try {
                FileObject::fromExisting((string)$document->file()?->fullPath())
                    ->delete();
            } catch (FileNotFoundException $e) {
                logger()->error($e->getMessage());
            }

            $document->delete();
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('documents');
        }

        flash()->success('Document deleted successfully');
        return $this->redirect('documents');
    }

    public function download($id = null): Redirect|Download
    {
        if (! isset($id)) {
            return $this->redirect('404');
        }

        try {
            $file = Document::go()->findOrFail($id)->file();
            if (! $file instanceof File) {
                throw new RecordNotFoundException;
            }
            return new Download(
                FileObject::fromExisting($file->fullPath()),
                $file->filename
            );
        } catch (RecordNotFoundException) {
            return $this->redirect('404');
        } catch (FileNotFoundException $e) {
            flash()->error($e->getMessage());
            return $this->redirect('documents');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            return $this->redirect('documents');
        }
    }

    protected function validationRules(): array
    {
        return [
            'filename' => ['required', 'min:4', 'max:100', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9_-]+$/'],
            'title' => ['required', 'min:4', 'max:100', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9()*,.\s-]+$/'],
            'type' => ['required', 'in:'.implode(',', DocumentType::values())],
            'issue_date' => ['required', 'date:Y-m-d'],
            'issuer_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9.\s-]+$/'],
            'issuer_email' => ['required', 'email'],
            'issuer_phone' => ['required', 'min:10', 'max:32', 'regex:/^\+?[0-9()-]+$/'],
            'storage' => ['required', 'max:50', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9,()\s-]+$/'],
            'keywords' => ['nullable', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9,\s-]+$/'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'filename.required' => 'Filename is required',
            'title.required' => 'Document title is required',
            'type.required' => 'Document type is required',
            'issue_date.required' => 'Issue date is required',
            'issuer_name.required' => 'Issuer name is required',
            'issuer_email.required' => 'Issuer email is required',
            'issuer_phone.required' => 'Issuer phone is required',
            'storage.required' => 'Physical storage is required',

            'filename.min' => 'Filename must be at least 4 characters long',
            'title.min' => 'Document title must be at least 4 characters long',
            'issuer_name.min' => 'Issuer name must be at least 2 characters long',
            'issuer_phone.min' => 'Issuer phone must be at least 10 characters long',

            'filename.max' => 'Filename must be at most 100 characters long',
            'title.max' => 'Document title must be at most 100 characters long',
            'issuer_name.max' => 'Issuer name must be at most 32 characters long',
            'issuer_phone.max' => 'Issuer phone must be at most 32 characters long',
            'storage.max' => 'Physical storage must be at most 50 characters long',

            'issuer_email.email' => 'Issuer email is invalid',
            'type.in' => 'Document type is invalid',
            'issue_date.date' => 'Issue date is invalid',

            'filename.regex' => 'Only alphabetic and numeric symbols, underscores, and hyphens are allowed',
            'title.regex' => 'Only alphabetic and numeric symbols, spaces, round brackets, asterisks, points, and hyphens are allowed',
            'issuer_name.regex' => 'Only alphabetic and numeric symbols, points, spaces, and hyphens are allowed',
            'issuer_phone.regex' => 'Only one leading plus symbol, numeric symbols, parentheses, and hyphens are allowed',
            'storage.regex' => 'Only alphabetic and numeric symbols, commas, round brackets, spaces, and hyphens are allowed',
            'keywords.regex' => 'Only alphabetic and numeric symbols, commas, spaces, and hyphens are allowed',
        ];
    }
}
