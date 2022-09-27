<?php
namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class SignupController extends Controller {

    protected $data;

    public function __construct() {
        $this->setData();
    }

    public function index() {
        // Show the signup form
        if (Helper::isUserLoggedIn()) {
            $_SESSION['flash'] = SIGNUP_ALREADY;
            return Helper::redirect(HOME);
        }
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        return $this->view('signup', $this->data);
    }

    public function init() {
        // Sign user up if the input is valid
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token']))
        return Helper::redirect(HOME.'404');

        $this->destroyToken();

        $params = [
            'firstname' => $_POST["fname"],
            'lastname'  => $_POST["lname"],
            'email'     => $_POST["email"],
            'username'  => $_POST["username"],
            'password'  => $_POST["password"],
        ];
        $user = new User($params);
        $signup = $user->signUp();

        if ($signup['valid']) {
            $_SESSION['flash'] = $signup['message'];
            return Helper::redirect(HOME.'login');
        }

        $this->data['fields']['password'] = $_POST["password"];
        foreach ($this->data['fields'] as $key => $value) {
            $this->data['fields'][$key] = $signup['input'][$key]['show'];
        }
        foreach ($this->data['errors'] as $key => $value) {
            $this->data['errors'][$key] = $signup['input'][$key]['error'];
        }
        foreach ($this->data['classes'] as $key => $value) {
            $this->data['classes'][$key] = $signup['input'][$key]['class'];
        }

        return $this->index();
    }

    protected function setData() {
        $this->data = [
            'title'   => PAGE_TITLES['signup'],
            'message' => '',
            'status'  => 'okay',
            'fields'  => [
                'firstname'   => '',
                'lastname'    => '',
                'email'       => '',
                'username'    => '',
                'password'    => '',
            ],
            'errors'  => [
                'firstname'   => '',
                'lastname'    => '',
                'email'       => '',
                'username'    => '',
                'password'    => '',
            ],
            'classes' => [
                'firstname'   => '',
                'lastname'    => '',
                'email'       => '',
                'username'    => '',
                'password'    => '',
            ],
        ];
    }
}
?>