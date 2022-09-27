<?php
namespace Digitaliseme\Models;

use Digitaliseme\Core\Helper;
use Digitaliseme\Core\Validator;
use Digitaliseme\Core\Database;

class SearchDocument extends Document {
    // Model of the document search
    protected $byTitle;
    protected $byDate;
    protected $byAgent;
    protected $byType;
    protected $byStorage;
    protected $byKeywords;

    public function __construct($params) {
        if (!is_array($params)) return;
        $this->setRawData($params, true);
        $this->setSearchCriteria();
    }

    public function getResults() {
        $validated = $this->validateSearchInput();
        if (!$validated['success'] && $validated['noinput']) {
            return [
                'valid' => false,
                'error' => NO_INPUT_ERROR,
            ];
        }
        if (!$validated['success'] && !$validated['noinput']) {
            return [
                'valid'   => false,
                'input'   => $validated['data'],
            ];
        }

        $results = $this->executeSearchQuery();
        if (!$results) return [
            'valid'   => true,
            'results' => false,
            'error'   => NO_SEARCH_RESULTS,
            'input'   => $validated['data'],
        ];
        return [
            'valid'   => true,
            'results' => $results,
        ];

    }

    protected function validateSearchInput() {
        if (!$this->isSetCriteria()) return [
            'success' => false,
            'noinput' => true,
        ];

        if ($this->byTitle) {
            $isValidDocTitle = Validator::validateDocTitle(
                $this->title,
                $this->sanitized['title']
            );
        }

        if ($this->byDate) {
            $isValidCreatedDate =
                Validator::validateCreatedDate($this->date);
        }

        if ($this->byAgent) {
            $isValidAgentName = Validator::validateAgentName(
                $this->agentName,
                $this->sanitized['agentName']
            );
        }

        if ($this->byStorage) {
            $isValidStoragePlace = Validator::validateStoragePlace(
                $this->storage,
                $this->sanitized['storage']
            );
        }

        if ($this->byKeywords) {
            $isValidKeywordsInput = Validator::validateKeywords(
                $this->keywords,
                $this->sanitized['keywords']
            );
        }

        $success =
            ($isValidDocTitle['result'] ?? true) &&
            ($isValidCreatedDate['result'] ?? true) &&
            ($isValidAgentName['result'] ?? true) &&
            ($isValidStoragePlace['result'] ?? true) &&
            ($isValidKeywordsInput['result'] ?? true);

        $default = [
            'result' => true,
            'error'  => '',
            'class'  => 'valid',
            'show'   => '',
        ];

        $data = [
            'docTitle'     => $isValidDocTitle ?? $default,
            'createdDate'  => $isValidCreatedDate ?? $default,
            'agentName'    => $isValidAgentName ?? $default,
            'storagePlace' => $isValidStoragePlace ?? $default,
            'keywords'     => $isValidKeywordsInput ?? $default,
        ];

        return [
            'success' => $success,
            'noinput' => false,
            'data'    => $data,
        ];
    }

    protected function executeSearchQuery() {
        $query = $this->prepareSearchQuery();
        $db = new Database();
        return $db->fetchMultipleRows(
            $query['sql'],
            $query['holders'],
            $query['values']
        );
    }

    protected function prepareSearchQuery() {
        $table = $this->setSearchTable();
        $columns = $this->setSearchColumns();
        $elements = $this->prepareSearchQueryElements();

        $condition = (count($elements['blocks']) == 1) ?
        $elements['blocks'][0] :
        implode(' AND ', $elements['blocks']);

        $holders = (count($elements['holders']) == 1) ?
        $elements['holders'][0] : $elements['holders'];

        $values = (count($elements['values']) == 1) ?
        $elements['values'][0] : $elements['values'];

        $sql = 'SELECT DISTINCT '.$columns;
        $sql .= 'FROM '.$table;
        $sql .= 'WHERE '.$condition;
        $sql .= ' ORDER BY saved DESC';

        return [
            'sql'     => $sql,
            'holders' => $holders,
            'values'  => $values,
        ];
    }

    protected function prepareSearchQueryElements() {
        $blocks = [];
        $holders = [];
        $values = [];

        if ($this->byTitle) {
            $blocks[] = 'doctitle LIKE :title';
            $holders[] = ':title';
            $values[] = $this->title;
        }

        if ($this->byDate) {
            $blocks[] = 'created LIKE :date';
            $holders[] = ':date';
            $values[] = $this->date;
        }

        if ($this->byAgent) {
            $blocks[] = 'name LIKE :agent';
            $holders[] = ':agent';
            $values[] = $this->agentName;
        }

        if ($this->byType) {
            $blocks[] = 'type LIKE :type';
            $holders[] = ':type';
            $values[] = $this->type;
        }

        if ($this->byStorage) {
            $blocks[] = 'place LIKE :storage';
            $holders[] = ':storage';
            $values[] = $this->storage;
        }

        if ($this->byKeywords) {
            $keywords = $this->prepareKeywordsForQuery();
            if (count($keywords['blocks']) == 1) {
                $blocks[] = $keywords['blocks'][0];
                $holders[] = $keywords['holders'][0];
                $values[] = $keywords['values'][0];
            } else {
                $keyBlock = '('.implode(' OR ', $keywords['blocks']).')';
                $blocks[] = $keyBlock;
                $holders = array_merge($holders, $keywords['holders']);
                $values = array_merge($values, $keywords['values']);
            }
        }

        $values = array_map([$this, 'addWildCards'], $values);

        return [
            'blocks'  => $blocks,
            'holders' => $holders,
            'values'  => $values,
        ];

    }

    protected function prepareKeywordsForQuery() {
        $blocks = [];
        $holders = [];
        $values = [];
        $keywords = Helper::getKeywordsArrayFrom($this->keywords);
        $index = 1;
        foreach ($keywords as $keyword) {
            $blocks[] = 'word LIKE :keyword'.$index;
            $holders[] = ':keyword'.$index;
            $values[] = $keyword;
            $index++;
        }
        return [
            'blocks'  => $blocks,
            'holders' => $holders,
            'values'  => $values,
        ];
    }

    protected function addWildCards($value) {
        return '%'.$value.'%';
    }

    protected function isSetCriteria() {
        $result =
            $this->byTitle ||
            $this->byDate ||
            $this->byAgent ||
            $this->byType ||
            $this->byStorage ||
            $this->byKeywords;
        return $result;
    }

    protected function setSearchCriteria() {
        $this->byTitle = !empty($this->title);
        $this->byDate = !empty($this->date);
        $this->byAgent = !empty($this->agentName);
        $this->byType = !empty($this->type);
        $this->byStorage = !empty($this->storage);
        $this->byKeywords = !empty($this->keywords);
    }

    protected function setSearchColumns() {
        $columns = 'documents.id, doctitle, created, saved, ';
        $columns .= 'name, type, place, documents.file_id ';
        return $columns;
    }

    protected function setSearchTable() {
        $table = 'documents JOIN doc_files ON file_id = doc_files.id ';
        $table .= 'JOIN agents ON agent_id = agents.id ';
        $table .= 'JOIN doc_types ON doctype_id = doc_types.id ';
        $table .= 'JOIN storage_places ON storage_id = storage_places.id ';
        $table .= 'JOIN filekeywords ';
        $table .= 'ON documents.file_id = filekeywords.file_id ';
        $table .= 'JOIN keywords ON keyword_id = keywords.id ';
        return $table;
    }

    protected function setType($type) {
        $this->type = $type;
    }
}
?>