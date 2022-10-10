<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;

class DocumentAgent {
    // Model of the document agent aka document creator
    protected $id;
    protected $name;
    protected $email;
    protected $phone;

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
            $agent = $this->read($params['id']);
            if (!is_object($agent)) return;
            $this->id = $agent->id;
            $this->name = $agent->name;
            $this->email = $agent->email;
            $this->phone = $agent->phone;
        } else {
            $this->setId($params['name']);
            $this->setName($params['name']);
            $this->setEmail($params['email']);
            $this->setPhone($params['phone']);
        }
    }

    public function getId() {
        return $this->id ?? false;
    }

    public function getName() {
        return $this->name ?? false;
    }

    public function getEmail() {
        return $this->email ?? false;
    }

    public function getPhone() {
        return $this->phone ?? false;
    }

    public function create() {
        if (isset($this->id)) return; //disable for existing agent
        $db = new Database();
        $sql = 'INSERT INTO agents(name, email, phone) values';
        $sql .= '(:name, :email, :phone)';
        $created = $db->insertIntoTable(
            $sql,
            [':name', ':email', ':phone'],
            [$this->name, $this->email, $this->phone]
        );
        if (!$created) {
            return [
                'success' => false,
                'error'   => config('app.messages.error.AGENT_DB_FAILURE'),
            ];
        } else {
            return [
                'success' => true,
                'id'      => $created,
            ];
        }
    }

    public function update($field, $newValue) {
        if (!isset($this->id)) return; //disable for non-existent agent
        $fields = ['email', 'phone'];
        if (!in_array($field, $fields)) return;
        $updated = $this->updateAgentRecord(
            $this->id,
            $field,
            $newValue
        );
        $error = $updated ? NULL : config('app.messages.error.AGENT_DB_FAILURE');
        return [
            'success' => $updated,
            'error'   => $error ?? false,
        ];
    }

    protected function read($id) {
        $db = new Database();
        $sql = 'SELECT * FROM agents WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function updateAgentRecord($id, $field, $newValue) {
        $holder = ':'.$field;
        $db = new Database();
        $sql = 'UPDATE agents SET '.$field.' = '.$holder.' WHERE id = :id';
        $updated = $db->updateTable(
            $sql,
            [$holder, ':id'],
            [$newValue, $id]
        );
        return $updated ? true : false;
    }

    protected function setId($name) {
        $db = new Database();
        $sql = 'SELECT id FROM agents WHERE name = :name';
        $agent = $db->fetchSingleRow($sql, ':name', $name);
        $this->id = is_object($agent) ? $agent->id : NULL;
    }

    protected function setName($name) {
        $this->name = $name;
    }

    protected function setEmail($email) {
        $this->email = $email;
    }

    protected function setPhone($phone) {
        $this->phone = $phone;
    }
}
