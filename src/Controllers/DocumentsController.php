<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Helper;
use Digitaliseme\Enumerations\DocumentType;
use Digitaliseme\Models\Document;
use Digitaliseme\Models\File;
use Digitaliseme\Models\UploadedFile;
use Digitaliseme\Models\RawDocument;
use Digitaliseme\Models\ArchiveDocument;
use Throwable;

class DocumentsController extends Controller
{
    protected array $data;

    public function __construct() {
        $this->setData();
    }

    public function index(): void
    {
        try {
            $records = (new Document)->query()->get();
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            $this->view('documents/index', $this->data);
        }

        $documents = $records ?? [];

        if (count($documents) === 0) {
            flash()->info(config('app.messages.info.NO_DOCUMENTS'));
        }

        $this->data['documents'] = $documents;

        $this->view('documents/index', $this->data);
    }

    public function create($id = null): void
    {
        if (! isset($id)) {
            $this->redirect('404');
        }

        $this->data['title'] = config('app.page.titles')['documents/create'];

        try {
            /** @var File $file */
            $file = (new File)->query()
                ->where('id', '=', $id)
                ->where('user_id', '=', $_SESSION["loggedinID"])
                ->whereNull('document_id')
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            $this->redirect('404');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            $this->view('documents/_create', $this->data);
        }

        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->data['filename'] = $file->filename;
        $_SESSION['upfile'] = $file->id;

        $this->view('documents/_create', $this->data);
    }

    /**
     * @throws ValidatorException
     */
    public function store()
    {
        if (! $this->isPostRequest() ||
            ! $this->isValidToken($_POST['token'])
        ) {
            $this->redirect('404');
        }

        $this->destroyToken();
        $fileId = $_SESSION['upfile'] ?? null;

        if (empty($fileId) || $fileId !== (int) $_POST['fileId']) {
            flash()->error(config('app.messages.error.GENERAL_ERROR'));
            $this->redirect('uploads');
        }

        unset($_SESSION['upfile']);

        $validator = $this->validate($_POST, [
            'filename' => ['required', 'min:4', 'max:100', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9_-]+$/'],
            'title' => ['required', 'min:4', 'max:100', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9()*,.\s-]+$/'],
            'type' => ['required', 'in:'.implode(',', DocumentType::values())],
            'issue_date' => ['required'],
            'issuer_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ-]+$/'],
            'issuer_email' => ['required', 'email'],
            'issuer_phone' => ['required', 'min:10', 'max:32', 'regex:/^[0-9+()-]+$/'],
            'storage' => ['required', 'max:50', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9,()\s-]+$/'],
            'keywords' => ['regex:/^\s*([^,\s-]{2,}\s?[^,\s-]*){1,}\s*,\s*([^\s-]{2,}\s?[^\s-]*)*\s*$/'],
        ], [
            'filename.required' => 'Filename is required',
            'title.required' => 'Document title is required',
            'type.required' => 'Document type is required',
            'issue_date.required' => 'Issue date is required',
            'issuer_name.required' => 'Issuer name is required',
            'issuer_email.required' => 'Issuer email is required',
            'issuer_phone.required' => 'Issuer phone is required',
            'storage.required' => 'Physical storage is required',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->getErrors())->redirect('documents/create/'.$fileId);
        }

        // Logic for creating the document

        if (true) {
            flash()->success(config('app.messages.info.NEW_DOC_OK'));
            $this->redirect('documents');
        }

        flash()->error(config('app.messages.error.GENERAL_ERROR'));
        $this->redirect('documents/create/'.$fileId);
    }

    public function show($id = NULL) {
        // Fetch and show the details of the document
        if (!isset($id)) return Helper::redirect(config('app.url').'404');

        $this->data['title'] = config('app.page.titles')['documents/show'];

        $document = new ArchiveDocument($id);
        $details = $document->getDetails();

        if (!$details['exist']) {
            $this->data['status'] = 'error';
            $this->data['message'] = $details['error'];
        } else {
            foreach ($this->data['fields'] as $key => $value) {
                $this->data['fields'][$key] = $details['data'][$key];
            }
            $this->data['selectedType'] = $details['data']['docType'];
            $this->data['userId'] = $details['data']['userId'];
            $this->data['docId'] = $id;
        }

        unset($_SESSION['flash']);
        unset($_SESSION['status']);
        return $this->view('documents/show', $this->data);
    }

    public function edit($id = NULL) {
        // Show forms for updating and deleting the document
        if (!isset($id)) return Helper::redirect(config('app.url').'404');

        $this->data['title'] = config('app.page.titles')['documents/edit'];

        $this->destroyToken();
        $document = new ArchiveDocument($id);
        $details = $document->getDetails();

        if (!$details['exist']) {
            $this->data['status'] = 'error';
            $this->data['message'] = $details['error'];
            return $this->view('documents/edit', $this->data);
        }

        if ($details['data']['userId'] !== $_SESSION['loggedinID']) {
            $this->data['status'] = 'error';
            $this->data['message'] = config('app.messages.error.DOCUMENT_EDIT_AUTH_ERROR');
            return $this->view('documents/edit', $this->data);
        }

        foreach ($this->data['fields'] as $key => $value) {
            !empty($this->data['fields'][$key]) ?:
            $this->data['fields'][$key] = $details['data'][$key];
        }
        !empty($this->data['selectedType']) ?:
        $this->data['selectedType'] = $details['data']['docType'];
        $this->data['docId'] = $id;
        $this->data['token'] = $this->generateToken();

        return $this->view('documents/edit', $this->data);
    }

    public function update($id = NULL) {
        // Update the document if user input is valid
        if (!isset($id) ||
            !$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(config('app.url').'404');

        $this->destroyToken();

        $document = new ArchiveDocument($id);
        $params = [
            'first_name'    => $_POST["first_name"],
            'doctitle' => $_POST["doctitle"],
            'created'  => $_POST["created"],
            'agname'   => $_POST["agname"],
            'agemail'  => $_POST["agemail"],
            'agphone'  => $_POST["agphone"],
            'doctype'  => $_POST["doctype"],
            'storage'  => $_POST["storage"],
            'keywords' => $_POST["keywords"],
        ];
        $update = $document->update($params);

        if ($update['valid']) {
            $_SESSION['status'] = $update['class'];
            $_SESSION['flash'] = $update['message'];
            return Helper::redirect(config('app.url').'documents/show/'.$id);
        }

        $this->data['selectedType'] = $_POST["doctype"];
        foreach ($this->data['fields'] as $key => $value) {
            $this->data['fields'][$key] = $update['input'][$key]['show'];
        }
        foreach ($this->data['errors'] as $key => $value) {
            $this->data['errors'][$key] = $update['input'][$key]['error'];
        }
        foreach ($this->data['classes'] as $key => $value) {
            $this->data['classes'][$key] = $update['input'][$key]['class'];
        }

        return $this->edit($id);
    }

    public function delete($id = NULL) {
        // Delete the document
        if (!isset($id) ||
            !$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(config('app.url').'404');

        $this->destroyToken();

        $document = new ArchiveDocument($id);
        $delete = $document->delete();

        if ($delete['success']) {
            $_SESSION['flash'] = $delete['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['flash'] = $delete['error'];
        }

        return Helper::redirect(config('app.url').'documents');
    }

    public function download($id = NULL) {
        // Force download of the document
        if (!isset($id)) return Helper::redirect(config('app.url').'404');

        $document = new ArchiveDocument($id);
        $download = $document->download();

        if (!$download['success']) {
            $_SESSION['status'] = 'error';
            $_SESSION['flash'] = $download['error'];
        }

        return Helper::redirect(config('app.url').'documents');
    }

    protected function setData(): void
    {
        $this->data = [
            'title' => config('app.page.titles')['documents'],
            'filename' => null,
            'documents' => [],
        ];
    }
}
