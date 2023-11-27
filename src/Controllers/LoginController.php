<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;
use Throwable;

class LoginController extends Controller
{
    protected array $data;

    public function __construct()
    {
        $this->setData();
    }

    /**
     * Show the login form
     */
    public function index(): void
    {
        if (Helper::isUserLoggedIn()) {
            $_SESSION['flash'] = config('app.messages.info.LOGIN_ALREADY');
            Helper::redirect(config('app.url'));
        }
        unset($_SESSION['flash']);
        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->view('login', $this->data);
    }

    /**
     * Log user in if the input is valid
     */
    public function init(): void
    {
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token'])
        ) {
            Helper::redirect(config('app.url') . '404');
        }

        $this->destroyToken();

        $params = [
            'username' => $_POST["username"],
            'password' => $_POST["password"]
        ];

        $validLogin = false;

        try {
            $user = (new User)->query()
                ->where('uname', '=', $params['username'])
                ->first();
            $password = (string) $user?->password;
            if (password_verify($params['password'], $password)) {
                $validLogin = true;
            }
        } catch (Throwable) {
            // Log error
        }

        if (! $validLogin) {
            $this->data['classes']['fields'] = 'invalid';
            $this->data['classes']['message'] = 'error';
            $this->data['fields']['username'] = $params['username'];
            $this->data['fields']['password'] = $params['password'];
            $this->data['message'] = config('app.messages.error.LOGIN_ERROR');
            $this->index();

            return;
        }

        /** @var User $user */
        $_SESSION["loggedin"] = $user->uname;
        $_SESSION["loggedinName"] = $user->fname;
        $_SESSION["loggedinID"] = $user->id;
        $_SESSION['flash'] = config('app.messages.info.LOGIN_OK');
        Helper::redirect(config('app.url'));
    }

    protected function setData(): void
    {
        $this->data = [
            'title'   => config('app.page.titles')['login'],
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
