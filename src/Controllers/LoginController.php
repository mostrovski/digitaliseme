<?php
namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class LoginController extends Controller {

    protected $data;

    public function __construct() {
        $this->setData();
    }

    public function index() {
        // Show the login form
        if (Helper::isUserLoggedIn()) {
            $_SESSION['flash'] = LOGIN_ALREADY;
            return Helper::redirect(HOME);
        }
        unset($_SESSION['flash']);
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        return $this->view('login', $this->data);
    }

    public function init() {
        // Log user in if the input is valid
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();

        $params = [
            'username' => $_POST["username"],
            'password' => $_POST["password"]
        ];
        $user = new User($params);
        $login = $user->logIn();

        if (!$login['success']) {
            $this->data['classes']['fields'] = 'invalid';
            $this->data['classes']['message'] = 'error';
            $this->data['fields']['username'] = $login['input']['username'];
            $this->data['fields']['password'] = $login['input']['password'];
            $this->data['message'] = $login['error'];
            return $this->index();
        }

        $_SESSION['flash'] = $login['message'];
        return Helper::redirect(HOME);
    }

    protected function setData() {
        $this->data = [
            'title'   => PAGE_TITLES['login'],
            'message' => $_SESSION['flash'] ?? '',
            'fields'  => [
                'username' => '',
                'password' => '',
            ],
            'classes' => [
                'message' => 'okay',
                'fields'  => '',
            ],
        ];
    }
}
?>