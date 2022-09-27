<?php
namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;

class DocumentType {
    // Model of the document type
    protected $id;
    protected $type;

    public function __construct($params) {
        /*
         * Flexible construction: parameters may or may not include the id.
         * Depending on where and when the class is instantiated, the id
         * may or may not be known.
         */
        if (!is_array($params)) return;
        if (isset($params['id'])) {
            $record = $this->read($params['id']);
            if (!is_object($record)) return;
            $this->id = $record->id;
            $this->type = $record->type;
        } else {
            $this->setId($params['type']);
            $this->setType($params['type']);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    protected function read($id) {
        $db = new Database();
        $sql = 'SELECT * FROM doc_types WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function setId($type) {
        $db = new Database();
        $sql = 'SELECT id FROM doc_types WHERE type = :type';
        $this->id = $db->fetchSingleRow($sql, ':type', $type)->id;
    }

    protected function setType($type) {
        $this->type = $type;
    }
}
?>