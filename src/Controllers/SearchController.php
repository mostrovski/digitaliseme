<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\SearchDocument;

class SearchController extends Controller {

    protected $data;

    public function __construct() {
        $this->setData();
    }

    public function index() {
        // Show a search form
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        return $this->view('search/index', $this->data);
    }

    public function find() {
        // Show search results or return a search form
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();

        $params = [
            'doctitle' => $_POST["doctitle"],
            'created'  => $_POST["created"],
            'agname'   => $_POST["agname"],
            'doctype'  => $_POST["doctype"],
            'storage'  => $_POST["storage"],
            'keywords' => $_POST["keywords"],
        ];
        $search = new SearchDocument($params);
        $request = $search->getResults();

        if ($request['valid'] && $request['results']) {
            $this->data['results'] = $request['results'];
        } else {
            $this->data['status'] = 'error';
            $this->data['message'] = $request['error'] ?? '';
        }
        if (isset($request['input'])) {
            $this->data['selectedType'] = $_POST["doctype"];
            foreach ($this->data['fields'] as $key => $value) {
                $this->data['fields'][$key] =
                    $request['input'][$key]['show'];
            }
            foreach ($this->data['errors'] as $key => $value) {
                $this->data['errors'][$key] =
                    $request['input'][$key]['error'];
            }
            foreach ($this->data['classes'] as $key => $value) {
                $this->data['classes'][$key] =
                    $request['input'][$key]['class'];
            }
        }

        return $this->index();
    }

    protected function setData() {
        $this->data = [
            'title'        => PAGE_TITLES['search'],
            'message'      => '',
            'status'       => 'okay',
            'docTypes'     => Helper::fetchDocumentTypes(),
            'selectedType' => '',
            'fields'       => [
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
            'errors'       => [
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
            'classes'      => [
                'docTitle'     => '',
                'createdDate'  => '',
                'agentName'    => '',
                'storagePlace' => '',
                'keywords'     => '',
            ],
        ];
    }
}
