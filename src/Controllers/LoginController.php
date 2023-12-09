<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
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
            flash()->info(config('app.messages.info.LOGIN_ALREADY'));
            $this->redirect('/');
        }

        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->view('login', $this->data);
    }

    /**
     * Log user in if the input is valid
     * @throws ValidatorException
     */
    public function init(): void
    {
        if (!$this->isPostRequest() ||
            !$this->isValidToken($_POST['token'])
        ) {
            $this->redirect('404');
        }

        $this->destroyToken();

        $params = [
            'username' => $_POST["username"],
            'password' => $_POST["password"]
        ];

        $validator = $this->validate($params, [
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->getErrors())->redirect('login');
        }

        $validCredentials = false;

        try {
            $user = (new User)->query()
                ->where('username', '=', $params['username'])
                ->first();
            $password = (string) $user?->password;
            if (password_verify($params['password'], $password)) {
                $validCredentials = true;
            }
        } catch (Throwable) {
            // Log error
        }

        if (! $validCredentials) {
            flash()->error(config('app.messages.error.LOGIN_ERROR'));
            $this->redirect('login');
        }

        /** @var User $user */
        $_SESSION["loggedin"] = $user->username;
        $_SESSION["loggedinName"] = $user->first_name;
        $_SESSION["loggedinID"] = $user->id;

        flash()->success(config('app.messages.info.LOGIN_OK'));
        $this->redirect('/');
    }

    protected function setData(): void
    {
        $this->data = [
            'title' => config('app.page.titles')['login'],
        ];
    }
}
