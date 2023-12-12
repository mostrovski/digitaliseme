<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Helper;

class ArchiveDocument extends OldDocument {
    // Model of the existing document
    protected $id;
    protected $docTitle;
    protected $created;
    protected $docAgent;
    protected $docType;
    protected $docStorage;
    protected $docFile;
    protected $uderId;

    public function __construct($id) {
        $doc = $this->readDocumentRecord($id);
        if (!is_object($doc)) return;
        $this->setId($doc->id);
        $this->setDocTitle($doc->doctitle);
        $this->setCreated($doc->created);
        $this->setUserId($doc->user_id);
        $this->setDocAgent($doc->agent_id);
        $this->setDocType($doc->doctype_id);
        $this->setDocStorage($doc->storage_id);
        $this->setDocFile($doc->file_id);
    }

    public function getDetails() {
        if (!isset($this->id)) {
            return [
                'exist' => false,
                'error' => config('app.messages.error.NO_DOCUMENT_ERROR'),
            ];
        }
        return [
            'exist' => true,
            'data'  => $this->fetchDocumentDetails($this->id),
        ];
    }

    public function download() {
        if (!isset($this->id)) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.NO_DOCUMENT_ERROR'),
            ];
        }
        $downloaded = $this->docFile->download();
        if (!$downloaded['success']) {
            return [
                'success' => false,
                'error'   => $downloaded['error'],
            ];
        }
        return [
            'success' => true,
            'message' => config('app.messages.info.DOWNLOAD_OK'),
        ];
    }

    public function delete() {
        if (!isset($this->id)) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.NO_DOCUMENT_ERROR'),
            ];
        }
        if ($this->userId !== $_SESSION["loggedinID"]) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.DOCUMENT_DELETE_AUTH_ERROR'),
            ];
        }
        $this->unbindKeywords($this->docFile->getId());
        $deleteFile = $this->docFile->delete();
        if (!$deleteFile['success']) {
            return [
                'success' => false,
                'error'   => $deleteFile['error'] ?? config('app.messages.error.GENERAL_ERROR'),
            ];
        }
        if (!$this->deleteDocumentRecord($this->id)) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.DOCUMENT_DELETE_RECORD_FAILURE'),
            ];
        }
        return [
            'success' => true,
            'message' => config('app.messages.info.DELETE_DOC_OK'),
        ];
    }

    public function update($params) {
        if (!is_array($params)) return;
        if (!isset($this->id)) {
            return [
                'valid'   => true,
                'class'   => 'error',
                'message' => config('app.messages.error.NO_DOCUMENT_ERROR'),
            ];
        }
        if ($this->userId !== $_SESSION["loggedinID"]) {
            return [
                'valid'   => true,
                'class'   => 'error',
                'message' => config('app.messages.error.DOCUMENT_UPDATE_AUTH_ERROR'),
            ];
        }
        $this->setRawData($params);
        $validated = $this->validate();
        if (!$validated['success']) return [
            'valid' => false,
            'input' => $validated['data'],
        ];
        $data = $this->prepareDataForUpdate();
        if (!$data['prepared']) return [
            'valid'   => true,
            'class'   => 'error',
            'message' => $data['error'],
        ];
        if ($this->shouldUpdateDocumentRecord($data['params'])) {
            $updated = $this->updateDocumentRecord($data['params']);
            if (!$updated['success']) return [
                'valid'   => true,
                'class'   => 'error',
                'message' => $updated['error'],
            ];
        }
        if ($data['newPath']) {
            $moved = $this->docFile->moveToNewDirectory($data['newPath']);
            if (!$moved['success']) return [
                'valid'   => true,
                'class'   => 'error',
                'message' => $moved['error'],
            ];
        }
        return [
            'valid'   => true,
            'class'   => 'okay',
            'message' => config('app.messages.info.UPDATE_DOC_OK'),
        ];
    }

    protected function shouldUpdateDocumentRecord($data) {
        $result =
            $data['title'] !== $this->docTitle ||
            $data['date'] !== $this->created ||
            $data['agent'] !== $this->docAgent->getId() ||
            $data['type'] !== $this->docType->getId() ||
            $data['storage'] !== $this->docStorage->getId();
        return $result;
    }

    protected function prepareDataForUpdate() {
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
        $keywords = $this->prepareKeywords($this->docFile->getId());
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
            'id'      => $this->id,
        ];
        return [
            'prepared' => true,
            'params'   => $data,
            'newPath'  => $file['newPath'],
        ];
    }

    protected function prepareFile() {
        if ($this->fileName !== $this->docFile->getName()) {
            $updated = $this->docFile->update('filename', $this->fileName);
            $nameSuccess = $updated['success'];
            $nameErr = $updated['error'];
        }
        if ($this->type->getId() !== $this->docType->getId()) {
            $oldPath = $this->docFile->getPath();
            $dir = Helper::defineDirectoryFor($this->type->getType());
            $newPath = Helper::redefinePath($oldPath, $dir);
            $updated = $this->docFile->update('filepath', $newPath);
            $pathSuccess = $updated['success'];
            $pathErr = $updated['error'];
        }
        if (isset($nameSuccess) && isset($pathSuccess)) {
            $success = $nameSuccess && $pathSuccess;
            $error = $success ? NULL : ($nameErr ? $nameErr : $pathErr);
        } else {
            $success = $nameSuccess ?? $pathSuccess ?? true;
            $error = $success ? NULL : ($nameErr ?? $pathErr);
        }
        return [
            'success' => $success,
            'error'   => $error,
            'newPath' => $newPath ?? false,
        ];
    }

    protected function prepareKeywords($fileId) {
        $this->unbindKeywords($fileId);
        return $this->manageDocumentKeywords($fileId);
    }

    protected function prepareAgent() {
        if ($this->agentName !== $this->docAgent->getName()) {
            return $this->manageDocumentAgent();
        }
        if ($this->agentEmail !== $this->docAgent->getEmail()) {
            $updated = $this->docAgent->update('email', $this->agentEmail);
            $emailSuccess = $updated['success'];
            $emailErr = $updated['error'];
        }
        if ($this->agentPhone !== $this->docAgent->getPhone()) {
            $updated = $this->docAgent->update('phone', $this->agentPhone);
            $phoneSuccess = $updated['success'];
            $phoneErr = $updated['error'];
        }
        if (isset($emailSuccess) && isset($phoneSuccess)) {
            $success = $emailSuccess && $phoneSuccess;
            $error = $success ? NULL : ($emailErr ? $emailErr : $phoneErr);
        } else {
            $success = $emailSuccess ?? $phoneSuccess ?? true;
            $error = $success ? NULL : ($emailErr ?? $phoneErr);
        }
        return [
            'success' => $success,
            'error'   => $error,
            'id'      => $this->docAgent->getId(),
        ];
    }

    protected function prepareStorage() {
        if ($this->storage !== $this->docStorage->getPlace()) {
            return $this->manageDocumentStorage();
        }
        return ['success' => true, 'id' => $this->docStorage->getId()];
    }

    protected function setId($id) {
        $this->id = $id;
    }

    protected function setDocTitle($title) {
        $this->docTitle = $title;
    }

    protected function setCreated($date) {
        $this->created = $date;
    }

    protected function setDocAgent($id) {
        $this->docAgent = new DocumentAgent(['id' => $id]);
    }

    protected function setDocType($id) {
        $this->docType = new DocumentType(['id' => $id]);
    }

    protected function setDocStorage($id) {
        $this->docStorage = new DocumentStorage(['id' => $id]);
    }

    protected function setDocFile($id) {
        $this->docFile = new DocumentOldFile(['id' => $id]);
    }

    protected function setUserId($id) {
        $this->userId = $id;
    }
}
