<?php
namespace Models;

use Core\Helper;
use Core\Validator;
use Core\Database;

abstract class Document {
    // Base class for RawDocument, ArchiveDocument, and SearchDocument
    protected $fileName;
    protected $title;
    protected $date;
    protected $agentName;
    protected $agentEmail;
    protected $agentPhone;
    protected $type;
    protected $storage;
    protected $keywords;
    protected $sanitized = [];

    protected function fetchDocumentDetails($id) {
        $doc = $this->readDocumentRecord($id);
        $agent = new DocumentAgent(['id' => $doc->agent_id]);
        $type = new DocumentType(['id' => $doc->doctype_id]);
        $storage = new DocumentStorage(['id' => $doc->storage_id]);
        $file = new DocumentFile(['id' => $doc->file_id]);
        $keywords = $this->fetchKeywords($doc->file_id);
        $details = [
            'docId'        => $doc->id,
            'docTitle'     => $doc->doctitle,
            'createdDate'  => $doc->created,
            'agentId'      => $doc->agent_id,
            'docTypeId'    => $doc->doctype_id,
            'storageId'    => $doc->storage_id,
            'fileId'       => $doc->file_id,
            'userId'       => $doc->user_id,
            'fileName'     => $file->getName(),
            'filePath'     => $file->getPath(),
            'agentName'    => $agent->getName(),
            'agentEmail'   => $agent->getEmail(),
            'agentPhone'   => $agent->getPhone(),
            'docType'      => $type->getType(),
            'storagePlace' => $storage->getPlace(),
            'keywords'     => $keywords,
        ];
        return $details;
    }

    protected function fetchKeywords($fileId) {
        $keyArray = [];
        $db = new Database();
        $sql = 'SELECT file_id, word FROM keywords JOIN filekeywords ';
        $sql .= 'ON keywords.id = filekeywords.keyword_id ';
        $sql .= 'WHERE file_id = :id';
        $files = $db->fetchMultipleRows($sql, ':id', $fileId);
        foreach ($files as $file) {
            $keyArray[] = $file->word;
        }
        return Helper::setKeywordsFrom($keyArray);
    }

    protected function manageDocumentAgent() {
        $params = [
            'name'  => $this->agentName,
            'email' => $this->agentEmail,
            'phone' => $this->agentPhone,
        ];
        $agent = new DocumentAgent($params);
        $id = $agent->getId();
        return $id ? ['success' => true, 'id' => $id] : $agent->create();
    }

    protected function manageDocumentStorage() {
        $params = ['place' => $this->storage];
        $storage = new DocumentStorage($params);
        $id = $storage->getId();
        return $id ? ['success' => true, 'id' => $id] : $storage->create();
    }

    protected function manageDocumentKeywords($fileId) {
        $keywords = Helper::getKeywordsFrom($this->keywords);
        foreach ($keywords as $word) {
            $keyword = new DocumentKeyword(['word' => $word]);
            if (!$keyword->getId()) {
                $created = $keyword->create();
                if (!$created['success']) return [
                    'success' => false,
                    'error'   => $created['error'],
                ];
            }
            $keyword->bindToFile($fileId);
        }
        return ['success' => true];
    }

    protected function unbindKeywords($fileId) {
        $db = new Database();
        $sql = 'DELETE FROM filekeywords WHERE file_id = :file_id';
        $db->deleteFromTable($sql, ':file_id', $fileId);
    }

    protected function createDocumentRecord($params) {
        $db = new Database();
        $sql = 'INSERT INTO documents(doctitle, created, agent_id, ';
        $sql .= 'doctype_id, storage_id, file_id, user_id) values(';
        $sql .= ':title, :date, :agent, :type, :storage, :file, :user)';
        $created = $db->insertIntoTable(
            $sql,
            [':title',':date',':agent',':type',':storage',':file',':user'],
            [
                $params['title'],
                $params['date'],
                $params['agent'],
                $params['type'],
                $params['storage'],
                $params['file'],
                $params['user'],
            ]
        );
        return $created ?
        ['success' => true] :
        ['success' => false, 'error' => DOCUMENT_DB_FAILURE];
    }

    protected function readDocumentRecord($id) {
        $db = new Database();
        $sql = 'SELECT * FROM documents WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function updateDocumentRecord($params) {
        $db = new Database();
        $sql = 'UPDATE documents SET ';
        $sql .= 'doctitle = :title, created = :date, agent_id = :agent, ';
        $sql .= 'doctype_id = :type, storage_id = :storage ';
        $sql .= 'WHERE id = :id';
        $updated = $db->updateTable(
            $sql,
            [':title',':date',':agent',':type',':storage', ':id'],
            [
                $params['title'],
                $params['date'],
                $params['agent'],
                $params['type'],
                $params['storage'],
                $params['id'],
            ]
        );
        return $updated ?
        ['success' => true] :
        ['success' => false, 'error' => DOCUMENT_UPDATE_FAILURE];
    }

    protected function deleteDocumentRecord($id) {
        $db = new Database();
        $sql = 'DELETE FROM documents WHERE id = :id';
        $deleted = $db->deleteFromTable($sql, ':id', $id);
        return $deleted ? true : false;
    }

    protected function validate() {
        $isValidFileName = Validator::validateFileName(
            $this->fileName,
            $this->sanitized['fileName']
        );
        $isValidDocTitle = Validator::validateDocTitle(
            $this->title,
            $this->sanitized['title']
        );
        $isValidCreatedDate = Validator::validateCreatedDate($this->date);
        $isValidAgentName = Validator::validateAgentName(
            $this->agentName,
            $this->sanitized['agentName']
        );
        $isValidAgentEmail = Validator::validateInputEmail($this->agentEmail);
        $isValidAgentPhone = Validator::validateAgentPhone(
            $this->agentPhone,
            $this->sanitized['agentPhone']
        );
        $isValidStoragePlace = Validator::validateStoragePlace(
            $this->storage,
            $this->sanitized['storage']
        );
        $isValidKeywordsInput = Validator::validateKeywords(
            $this->keywords,
            $this->sanitized['keywords']
        );
        $success =
            $isValidFileName['result'] &&
            $isValidDocTitle['result'] &&
            $isValidCreatedDate['result'] &&
            $isValidAgentName['result'] &&
            $isValidAgentEmail['result'] &&
            $isValidAgentPhone['result'] &&
            $isValidStoragePlace['result'] &&
            $isValidKeywordsInput['result'];
        $data = [
            'fileName'     => $isValidFileName,
            'docTitle'     => $isValidDocTitle,
            'createdDate'  => $isValidCreatedDate,
            'agentName'    => $isValidAgentName,
            'agentEmail'   => $isValidAgentEmail,
            'agentPhone'   => $isValidAgentPhone,
            'storagePlace' => $isValidStoragePlace,
            'keywords'     => $isValidKeywordsInput,
        ];
        return [
            'success' => $success,
            'data'    => $data,
        ];
    }

    protected function setRawData($params, $search=false) {
        $this->setTitle($params['doctitle']);
        $this->setDate($params['created']);
        $this->setAgentName($params['agname']);
        $this->setType($params['doctype']);
        $this->setStorage($params['storage']);
        $this->setKeywords($params['keywords']);
        if ($search) return;
        $this->setFileName($params['fname']);
        $this->setAgentEmail($params['agemail']);
        $this->setAgentPhone($params['agphone']);
    }

    protected function setFileName($name) {
        $sanitized = Validator::sanitize($name, FILE_NAME_PATTERN);
        $this->fileName = $sanitized['show'];
        $this->sanitized['fileName'] = $sanitized['result'];
    }

    protected function setTitle($title) {
        $sanitized = Validator::sanitize($title, DOC_TITLE_PATTERN);
        $this->title = $sanitized['show'];
        $this->sanitized['title'] = $sanitized['result'];
    }

    protected function setDate($date) {
        $this->date = $date;
    }

    protected function setAgentName($name) {
        $sanitized = Validator::sanitize($name, AGENT_NAME_PATTERN);
        $this->agentName = $sanitized['show'];
        $this->sanitized['agentName'] = $sanitized['result'];
    }

    protected function setAgentEmail($email) {
        $sanitized = Validator::sanitize($email, EMAIL_SAN_PATTERN);
        $this->agentEmail = $sanitized['show'];
        $this->sanitized['agentEmail'] = $sanitized['result'];
    }

    protected function setAgentPhone($phone) {
        $sanitized = Validator::sanitize($phone, PHONE_PATTERN);
        $this->agentPhone = $sanitized['show'];
        $this->sanitized['agentPhone'] = $sanitized['result'];
    }

    protected function setType($type) {
        $this->type = new DocumentType(['type' => $type]);
    }

    protected function setStorage($place) {
        $sanitized = Validator::sanitize($place, STORAGE_NAME_PATTERN);
        $this->storage = $sanitized['show'];
        $this->sanitized['storage'] = $sanitized['result'];
    }

    protected function setKeywords($keywords) {
        $keywords = strlen(trim($keywords)) && !strpos($keywords, ',') ?
        $keywords.',' : $keywords;
        $sanitized = Validator::sanitize($keywords, KEYWORDS_SAN_PATTERN);
        $this->keywords = $sanitized['show'];
        $this->sanitized['keywords'] = $sanitized['result'];
    }
}
?>