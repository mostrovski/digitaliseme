<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\RawFile;
use Digitaliseme\Models\UploadedFile;

class UploadsController extends Controller {

    protected $data;

    public function __construct() {
        $this->setData();
    }

    public function index() {
        // Show existing uploads
        $this->data['title'] = config('app.page.titles')['uploads'];
        $uploads = Helper::fetchUploads();
        if (!$uploads) {
            $this->data['message'] = $_SESSION['flash'] ?? config('app.messages.info.NO_UPLOADS');
        } else {
            $this->data['uploads'] = $uploads;
        }
        unset($_SESSION['flash']);
        unset($_SESSION['status']);
        return $this->view('uploads/index', $this->data);
    }

    public function create() {
        // Show the form for uploading a new file
        $this->data['title'] = config('app.page.titles')['uploads/create'];
        unset($_SESSION['flash']);
        unset($_SESSION['status']);
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        return $this->view('uploads/create', $this->data);
    }

    public function store() {
        // Save the uploaded file if it's valid
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(config('app.url').'404');

        $this->destroyToken();

        if (empty($_FILES['docfile']['name'])) {
            $_SESSION['flash'] = config('app.messages.error.NO_FILE_CHOSEN_ERROR');
            $_SESSION['status'] = 'error';
            return Helper::redirect(config('app.url').'uploads/create');
        }

        $upload = new RawFile(
            $_FILES['docfile']['name'],
            $_FILES['docfile']['size'],
            $_FILES['docfile']['tmp_name']
        );
        $store = $upload->saveFile();

        if (!$store['success']) {
            $_SESSION['flash'] = $store['error'];
            $_SESSION['status'] = 'error';
            return Helper::redirect(config('app.url').'uploads/create');
        }

        $_SESSION['flash'] = $store['message'];
        return Helper::redirect(config('app.url').'uploads');
    }

    public function delete($id = NULL) {
        // Delete uploaded file
        if (!isset($id)) return Helper::redirect(config('app.url').'404');

        $file = new UploadedFile($id);
        $delete = $file->delete();

        if ($delete['success']) {
            $_SESSION['flash'] = $delete['message'];
        } else {
            $_SESSION['flash'] = $delete['error'];
            $_SESSION['status'] = 'error';
        }

        return Helper::redirect(config('app.url').'uploads');
    }

    protected function setData() {
        $this->data = [
            'message' => $_SESSION['flash'] ?? '',
            'status'  => $_SESSION['status'] ?? 'okay',
        ];
    }
}
