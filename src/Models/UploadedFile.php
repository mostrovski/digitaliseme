<?php

namespace Digitaliseme\Models;

class UploadedFile extends File {
    // Model of the uploaded file
    protected $id;
    protected $path;
    protected $userId;

    public function __construct($id) {
        $this->setId($id);
        $file = $this->readFileRecord($id, 'uploads');
        if (!is_object($file)) return;
        $this->setPath($file->filepath);
        $this->setUserId($file->user_id);
    }

    public function read() {
        return $this->readFileRecord($this->id, 'uploads');
    }

    public function delete() {
        $file = $this->readFileRecord($this->id, 'uploads');
        if (!is_object($file)) {
            return [
                'success' => false,
                'error'   => NO_FILE_ERROR,
            ];
        }
        if ($this->userId !== $_SESSION["loggedinID"]) {
            return [
                'success' => false,
                'error'   => FILE_DELETE_AUTH_ERROR,
            ];
        }
        if (!$this->deleteFile($this->path)) {
            return [
                'success' => false,
                'error'   => FILE_DELETE_ERROR,
            ];
        }
        if (!$this->deleteFileRecord($this->id, 'uploads')) {
            return [
                'success' => false,
                'error'   => FILE_DELETE_RECORD_ERROR,
            ];
        }
        return ['success' => true, 'message' => UPLOAD_DELETE_OK];
    }

    public function moveToArchive($newPath) {
        if (!$this->relocateFile($this->path, $newPath)) {
            return [
                'success' => false,
                'error'   => FILE_TO_ARCHIVE_FAILURE,
            ];
        }
        if (!$this->deleteFileRecord($this->id, 'uploads')) {
            return [
                'success' => false,
                'error'   => FILE_DELETE_RECORD_ERROR,
            ];
        }
        return ['success' => true];
    }

    protected function setId($id) {
        $this->id = $id;
    }

    protected function setPath($path) {
        $this->path = $path;
    }

    protected function setUserId($userId) {
        $this->userId = $userId;
    }
}
