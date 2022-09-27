<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Helper;
use Digitaliseme\Core\Validator;
use Digitaliseme\Core\Database;

class RawFile extends File {
    // Model of the file that is being uploaded
    protected $fileName;
    protected $extension;
    protected $size;
    protected $tmpFilePath;
    protected $filePath;
    protected $userId;

    public function __construct($name, $size, $path) {
        $this->setFileName($name);
        $this->setExtension($name);
        $this->setSize($size);
        $this->setTmpFilePath($path);
        $this->setFilePath($this->extension);
        $this->setUserId();
    }

    public function saveFile() {
        $uploads = $this->getUploads();
        $file = $this->verifyUpload(
            $this->extension,
            $this->size,
            $this->fileName,
            $uploads
        );
        if (!$file['verified']) {
            return [
                'success' => false,
                'error'   => $file['error'],
            ];
        }
        $uploaded = $this->moveFileToUploads(
            $this->tmpFilePath,
            $this->filePath
        );
        if (!$uploaded) {
            return [
                'success' => false,
                'error'   => TRY_AGAIN_ERROR,
            ];
        }
        $saved = $this->addRecordToUploadsTable(
            $this->fileName,
            $this->filePath,
            $this->userId
        );
        if (!$saved) {
            return [
                'success' => false,
                'error'   => GENERAL_ERROR,
            ];
        }
        return [
            'success' => true,
            'message' => UPLOAD_OK,
        ];
    }

    protected function verifyUpload($ext, $size, $name, $uploads) {
        if (!in_array($ext, SUPPORTED_TYPES)) {
            $error = FILE_TYPE_ERROR;
        } else if ($size > SUPPORTED_SIZE) {
            $error = FILE_SIZE_ERROR;
        } else if ($size === 0) {
            $error = FILE_EMPTY_ERROR;
        } else if (!$this->isUniqueFile($name, $ext, $uploads, 'filename')) {
            $error = FILE_UNIQUE_ERROR;
        }
        return isset($error) ?
        ['verified' => false, 'error' => $error] :
        ['verified' => true];
    }

    protected function isUniqueFile($name, $ext, $source, $value) {
        if (Validator::isUnique($name, $source, $value)) {
            return true;
        }

        $db = new Database();
        $sql = 'SELECT * FROM uploads WHERE filename = :name';
        $file = $db->fetchSingleRow($sql, ':name', $name);

        return $ext != Helper::extractFileExtension($file->filepath);

    }

    protected function moveFileToUploads($tmp, $path) {
        $moved = move_uploaded_file($tmp, $path);
        return $moved;
    }

    protected function addRecordToUploadsTable($name, $path, $userId) {
        return $this->createFileRecord($name, $path, $userId);
    }

    protected function setFileName($name) {
        $name = htmlentities(Helper::extractFileName($name));
        $this->fileName = $name;
    }

    protected function setExtension($name) {
        $ext = Helper::extractFileExtension($name);
        $this->extension = $ext;
    }

    protected function setSize($size) {
        $this->size = $size;
    }

    protected function setTmpFilePath($path) {
        $this->tmpFilePath = $path;
    }

    protected function setFilePath($ext) {
        $name = uniqid();
        $path = 'app/uploads/'.$name.'.'.$ext;
        $this->filePath = $path;
    }

    protected function setUserId() {
        $this->userId = $_SESSION["loggedinID"];
    }

    protected function getUploads() {
        $db = new Database();
        $sql = 'SELECT filename FROM uploads';
        $uploads = $db->fetchWithoutParams($sql);
        return $uploads;
    }
}
