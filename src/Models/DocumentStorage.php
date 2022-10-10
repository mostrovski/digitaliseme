<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;

class DocumentStorage {
    // Model of the physical document storage
    protected $id;
    protected $place;

    public function __construct($params) {
        /*
         * Flexible construction: parameters may or may not include the id.
         * Depending on where and when the class is instantiated, the id
         * may or may not be known.
         * If parameters don't include the id, the constructor attempts to
         * set it using other parameters. Set or not set id property enables
         * or disables specific methods.
         */
        if (!is_array($params)) return;
        if (isset($params['id'])) {
            $storage = $this->read($params['id']);
            if (!is_object($storage)) return;
            $this->id = $storage->id;
            $this->place = $storage->place;
        } else {
            $this->setId($params['place']);
            $this->setPlace($params['place']);
        }
    }

    public function getId() {
        return $this->id ?? false;
    }

    public function getPlace() {
        return $this->place ?? false;
    }

    public function create() {
        if (isset($this->id)) return; //disable for existing storage
        $db = new Database();
        $sql = 'INSERT INTO storage_places(place) values(:place)';
        $created = $db->insertIntoTable($sql, ':place', $this->place);
        if (!$created) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.STORAGE_DB_FAILURE'),
            ];
        } else {
            return [
                'success' => true,
                'id'      => $created,
            ];
        }
    }

    protected function read($id) {
        $db = new Database();
        $sql = 'SELECT * FROM storage_places WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function setId($place) {
        $db = new Database();
        $sql = 'SELECT id FROM storage_places WHERE place = :place';
        $storage = $db->fetchSingleRow($sql, ':place', $place);
        $this->id = is_object($storage) ? $storage->id : NULL;
    }

    protected function setPlace($place) {
        $this->place = $place;
    }
}
