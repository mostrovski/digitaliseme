<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class SignupController extends Controller
{
    protected array $data;

    public function __construct() {
        $this->setData();
    }

    public function index(): void
    {
        if (Helper::isUserLoggedIn()) {
            flash()->info(config('app.messages.info.SIGNUP_ALREADY'));
            $this->redirect('/');
        }

        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->view('signup', $this->data);
    }

    /**
     * @throws ValidatorException
     */
    public function init(): void
    {
        if (! $this->isPostRequest() ||
            ! $this->isValidToken($_POST['token'])
        ) {
            $this->redirect('404');
        }

        $this->destroyToken();

        $params = [
            'first_name' => $_POST["first_name"],
            'last_name' => $_POST["last_name"],
            'email' => $_POST["email"],
            'username' => $_POST["username"],
            'password' => $_POST["password"],
        ];

        $validator = $this->validate($params, [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required'],
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->getErrors())->redirect('signup');
        }

        $user = new User($params);
        $signup = $user->signUp();

        if ($signup['valid']) {
            $_SESSION['flash'] = $signup['message'];
            Helper::redirect(config('app.url').'login');
        }

        $this->data['fields']['password'] = $_POST["password"];
        foreach ($this->data['fields'] as $key => $value) {
            $this->data['fields'][$key] = $signup['input'][$key]['show'];
        }

        $this->index();
    }

    protected function setData(): void
    {
        $this->data = [
            'title'   => config('app.page.titles')['signup'],
            'fields'  => [
                'first_name'   => '',
                'last_name'    => '',
                'email'       => '',
                'username'    => '',
                'password'    => '',
            ],
        ];
    }
}
