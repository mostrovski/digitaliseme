<?php
namespace Models;

use \Core\Helper;

class RawDocument extends Document {
    // Model of the document that is being created
    protected $uploadedFile;

    public function __construct($params) {
        if (!is_array($params)) return;
        $this->setRawData($params);
        $this->setUploadedFile($params['upfile']);
    }

    public function create() {
        $validated = $this->validate();
        if (!$validated['success']) return [
            'valid' => false,
            'input' => $validated['data'],
        ];
        $data = $this->prepareDataForCreate();
        if (!$data['prepared']) return [
            'valid'   => true,
            'class'   => 'error',
            'message' => $data['error'],
        ];
        $saved = $this->createDocumentRecord($data['params']);
        if (!$saved['success']) return [
            'valid'   => true,
            'class'   => 'error',
            'message' => $saved['error'],
        ];
        $moved = $this->uploadedFile->moveToArchive($data['path']);
        if (!$moved['success']) return [
            'valid'   => true,
            'class'   => 'error',
            'message' => $moved['error'],
        ];
        return [
            'valid'   => true,
            'class'   => 'okay',
            'message' => NEW_DOC_OK,
        ];
    }

    protected function prepareDataForCreate() {
        $agent = $this->prepareAgent();
        if (!$agent['success']) return [
            'prepared' => false,
            'error'    => $agent['error'],
        ];
        $storage = $this->prepareStorage();
        if (!$storage['success']) return [
            'prepared' => false,
            'error'    => $storage['error'],
        ];
        $file = $this->prepareFile();
        if (!$file['success']) return [
            'prepared' => false,
            'error'    => $file['error'],
        ];
        $keywords = $this->prepareKeywords($file['id']);
        if (!$keywords['success']) return [
            'prepared' => false,
            'error'    => $keywords['error'],
        ];
        $data = [
            'title'   => $this->title,
            'date'    => $this->date,
            'agent'   => $agent['id'],
            'type'    => $this->type->getId(),
            'storage' => $storage['id'],
            'file'    => $file['id'],
            'user'    => $_SESSION["loggedinID"],
        ];
        return [
            'prepared' => true,
            'params'   => $data,
            'path'     => $file['path'],
        ];
    }

    protected function prepareFile() {
        $oldPath = $this->uploadedFile->read()->filepath;
        $dir = Helper::defineDirectoryFor($this->type->getType());
        $newPath = Helper::redefinePath($oldPath, $dir);
        $params = ['fileName' => $this->fileName, 'filePath' => $newPath];
        $file = new DocumentFile($params);
        return $file->create();
    }

    protected function prepareKeywords($fileId) {
        return $this->manageDocumentKeywords($fileId);
    }

    protected function prepareAgent() {
        return $this->manageDocumentAgent();
    }

    protected function prepareStorage() {
        return $this->manageDocumentStorage();
    }

    protected function setUploadedFile($file) {
        $this->uploadedFile = $file;
    }
}
?>