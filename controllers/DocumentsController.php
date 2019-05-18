<?php
namespace Controllers;

use Core\Helper;
use Models\UploadedFile;
use Models\RawDocument;
use Models\ArchiveDocument;

class DocumentsController extends Controller {

    protected $data;

    public function __construct() {
        $this->setData();
    }

    public function index() {
        // Fetch and show existing documents
        $this->data['title'] = PAGE_TITLES['documents'];
        $documents = Helper::fetchDocuments();
        if (!$documents) {
            $this->data['message'] = $_SESSION['flash'] ?? NO_DOCUMENTS;
        } else {
            $this->data['documents'] = $documents;
        }
        unset($_SESSION['flash']);
        unset($_SESSION['status']);
        return $this->view('documents/index', $this->data);
    }

    public function create($id = NULL) {
        // Show a form for creating a new document from the file
        if (!isset($id)) return Helper::redirect(HOME.'404');

        $this->data['title'] = PAGE_TITLES['documents/create'];

        $file = new UploadedFile($id);
        $fileInfo = $file->read();

        if (!is_object($fileInfo)) {
            $this->data['status'] = 'error';
            $this->data['message'] = NO_FILE_ERROR;
            return $this->view('documents/create', $this->data);
        }

        if ($fileInfo->user_id !== $_SESSION["loggedinID"]) {
            $this->data['status'] = 'error';
            $this->data['message'] = DOCUMENT_WORKON_AUTH_ERROR;
            return $this->view('documents/create', $this->data);
        }

        !empty($this->data['fields']['fileName']) ?:
        $this->data['fields']['fileName'] = $fileInfo->filename;
        $this->destroyToken();
        $this->data['token'] = $this->createToken();
        $_SESSION['upfile'] = serialize($file);

        return $this->view('documents/create', $this->data);
    }

    public function store() {
        // Save the document if user input is valid
        if (!$this->isPostRequest() ||
            !$this->isTokenOk($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();
        $file = unserialize($_SESSION['upfile']);
        $id = $file->read()->id;
        unset($_SESSION['upfile']);

        $params = [
            'fname'    => $_POST["fname"],
            'doctitle' => $_POST["doctitle"],
            'created'  => $_POST["created"],
            'agname'   => $_POST["agname"],
            'agemail'  => $_POST["agemail"],
            'agphone'  => $_POST["agphone"],
            'doctype'  => $_POST["doctype"],
            'storage'  => $_POST["storage"],
            'keywords' => $_POST["keywords"],
            'upfile'   => $file,
        ];
        $rawDocument = new RawDocument($params);
        $document = $rawDocument->create();

        if ($document['valid']) {
            $_SESSION['status'] = $document['class'];
            $_SESSION['flash'] = $document['message'];
            return Helper::redirect(HOME.'documents');
        }

        $this->data['selectedType'] = $_POST["doctype"];
        foreach ($this->data['fields'] as $key => $value) {
            $this->data['fields'][$key] =
                $document['input'][$key]['show'];
        }
        foreach ($this->data['errors'] as $key => $value) {
            $this->data['errors'][$key] =
                $document['input'][$key]['error'];
        }
        foreach ($this->data['classes'] as $key => $value) {
            $this->data['classes'][$key] =
                $document['input'][$key]['class'];
        }

        return $this->create($id);
    }

    public function show($id = NULL) {
        // Fetch and show the details of the document
        if (!isset($id)) return Helper::redirect(HOME.'404');

        $this->data['title'] = PAGE_TITLES['documents/show'];

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
        if (!isset($id)) return Helper::redirect(HOME.'404');

        $this->data['title'] = PAGE_TITLES['documents/edit'];

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
            $this->data['message'] = DOCUMENT_EDIT_AUTH_ERROR;
            return $this->view('documents/edit', $this->data);
        }

        foreach ($this->data['fields'] as $key => $value) {
            !empty($this->data['fields'][$key]) ?:
            $this->data['fields'][$key] = $details['data'][$key];
        }
        !empty($this->data['selectedType']) ?:
        $this->data['selectedType'] = $details['data']['docType'];
        $this->data['docId'] = $id;
        $this->data['token'] = $this->createToken();

        return $this->view('documents/edit', $this->data);
    }

    public function update($id = NULL) {
        // Update the document if user input is valid
        if (!isset($id) ||
            !$this->isPostRequest() ||
            !$this->isTokenOk($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();

        $document = new ArchiveDocument($id);
        $params = [
            'fname'    => $_POST["fname"],
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
            return Helper::redirect(HOME.'documents/show/'.$id);
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
            !$this->isTokenOk($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();

        $document = new ArchiveDocument($id);
        $delete = $document->delete();

        if ($delete['success']) {
            $_SESSION['flash'] = $delete['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['flash'] = $delete['error'];
        }

        return Helper::redirect(HOME.'documents');
    }

    public function download($id = NULL) {
        // Force download of the document
        if (!isset($id)) return Helper::redirect(HOME.'404');

        $document = new ArchiveDocument($id);
        $download = $document->download();

        if (!$download['success']) {
            $_SESSION['status'] = 'error';
            $_SESSION['flash'] = $download['error'];
        }

        return Helper::redirect(HOME.'documents');
    }

    protected function setData() {
        $this->data = [
            'message'      => $_SESSION['flash'] ?? '',
            'status'       => $_SESSION['status'] ?? 'okay',
            'docTypes'     => Helper::fetchDocumentTypes(),
            'selectedType' => '',
            'fields'       => [
                'fileName'     => '',
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'agentEmail'   => '',
                'agentPhone'   => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
            'errors'       => [
                'fileName'     => '',
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'agentEmail'   => '',
                'agentPhone'   => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
            'classes'      => [
                'fileName'     => '',
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'agentEmail'   => '',
                'agentPhone'   => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
        ];
    }
}
?>