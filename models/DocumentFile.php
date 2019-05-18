<?php
namespace Models;

class DocumentFile extends File {
    // Model of the file attached to the document
    protected $id;
    protected $name;
    protected $path;

    public function __construct($params) {
        /*
         * Flexible construction: parameters may or may not include the id.
         * Depending on where and when the class is instantiated, the id
         * may or may not be known.
         * Set or not set id property enables or disables specific methods.
         */
        if (!is_array($params)) return;
        if (isset($params['id'])) {
            $file = $this->readFileRecord($params['id'], 'doc_files');
            if (!is_object($file)) return;
            $this->setId($file->id);
            $this->setName($file->filename);
            $this->setPath($file->filepath);
        } else {
            $this->setName($params['fileName']);
            $this->setPath($params['filePath']);
        }
    }

    public function getId() {
        return $this->id ?? false;
    }
    public function getName() {
        return $this->name ?? false;
    }
    public function getPath() {
        return $this->path ?? false;
    }

    public function create() {
        if (isset($this->id)) return; //disable for existing file
        $created = $this->createFileRecord($this->name, $this->path);
        if (!$created) {
            return [
                'success' => false,
                'error'   => FILE_DB_FAILURE,
            ];
        } else {
            return [
                'success' => true,
                'id'      => $created,
                'path'    => $this->path,
            ];
        }
    }

    public function read() {
        if (!isset($this->id)) return; //disable for non-existent file
        return $this->readFileRecord($this->id, 'doc_files');
    }

    public function update($field, $newValue) {
        $fields = ['filename', 'filepath'];
        if (!in_array($field, $fields)) return;
        if (!isset($this->id)) return; //disable for non-existent file
        $updated = ($field === 'filename') ?
        $this->updateName($newValue) :
        $this->updatePath($newValue);
        return [
            'success' => $updated['success'],
            'error'   => $updated['error'] ?? false,
        ];
    }

    public function delete() {
        if (!isset($this->id)) return; //disable for non-existent file
        if (!$this->deleteFile($this->path)) {
            return [
                'success' => false,
                'error'   => DOCUMENT_DELETE_FILE_FAILURE,
            ];
        }
        if (!$this->deleteFileRecord($this->id, 'doc_files')) {
            return [
                'success' => false,
                'error'   => FILE_DELETE_RECORD_ERROR,
            ];
        }
        return ['success' => true];
    }

    public function moveToNewDirectory($newPath) {
        if (!isset($this->id)) return; //disable for non-existent file
        if (!$this->relocateFile($this->path, $newPath)) {
            return [
                'success' => false,
                'error'   => FILE_DIR_UPDATE_FAILURE,
            ];
        }
        return ['success' => true];
    }

    public function download() {
        if (!isset($this->id)) return; //disable for non-existent file
        $downloaded = $this->downloadFile($this->path, $this->name);
        return [
            'success' => $downloaded['success'],
            'error'   => $downloaded['error'] ?? NULL,
        ];
    }

    protected function updateName($newName) {
        if (!$this->updateFileRecord($this->id, 'filename', $newName)) {
            return [
                'success' => false,
                'error'   => FILE_NAME_UPDATE_FAILURE,
            ];
        }
        return ['success' => true];
    }

    protected function updatePath($newPath) {
        if (!$this->updateFileRecord($this->id, 'filepath', $newPath)) {
            return [
                'success' => false,
                'error'   => FILE_PATH_UPDATE_FAILURE,
            ];
        }
        return ['success' => true];
    }

    protected function setId($id) {
        $this->id = $id;
    }

    protected function setName($name) {
        $this->name = $name;
    }

    protected function setPath($path) {
        $this->path = $path;
    }
}
?>