<?php
namespace Models;

use \Core\Database;

class DocumentKeyword {
    // Model of the document keyword
    protected $id;
    protected $word;

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
            $keyword = $this->read($params['id']);
            if (!is_object($keyword)) return;
            $this->id = $keyword->id;
            $this->word = $keyword->word;
        } else {
            $this->setId($params['word']);
            $this->setWord($params['word']);
        }
    }

    public function getId() {
        return $this->id ?? false;
    }

    public function bindToFile($fileId) {
        if (!isset($this->id)) return; //disable for non-existent keyword
        $db = new Database();
        $sql = 'INSERT INTO filekeywords(file_id, keyword_id) values';
        $sql .= '(:file_id, :keyword_id)';
        $db->insertIntoTable(
            $sql,
            [':file_id', ':keyword_id'],
            [$fileId, $this->id]
        );
    }

    public function create() {
        if (isset($this->id)) return; //disable for existing keyword
        $db = new Database();
        $sql = 'INSERT INTO keywords(word) values(:word)';
        $created = $db->insertIntoTable($sql, ':word', $this->word);
        if (!$created) {
            return [
                'success' => false,
                'error'   => KEYWORDS_DB_FAILURE,
            ];
        } else {
            $this->id = $created;
            return ['success' => true];
        }
    }

    protected function read($id) {
        $db = new Database();
        $sql = 'SELECT * FROM keywords WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function setId($word) {
        $db = new Database();
        $sql = 'SELECT id FROM keywords WHERE word = :word';
        $keyword = $db->fetchSingleRow($sql, ':word', $word);
        $this->id = is_object($keyword) ? $keyword->id : NULL;
    }

    protected function setWord($word) {
        $this->word = $word;
    }
}
?>