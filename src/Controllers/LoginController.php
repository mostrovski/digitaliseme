<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Models\User;
use Throwable;

class LoginController extends Controller
{
    protected array $data;

    public function __construct()
    {
        $this->setData();
    }
    public function index(): void
    {
        if (auth()->isIntact()) {
            flash()->info('You are logged in');
            $this->redirect('/');
        }

        $this->destroyToken();
        $this->data['token'] = $this->generateToken();
        $this->view('login', $this->data);
    }

    /**
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
            $user = User::go()->query()
                ->where('username', '=', $params['username'])
                ->first();
            $password = (string) $user?->password;
            if (password_verify($params['password'], $password)) {
                $validCredentials = true;
            }
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
        }

        if (! $validCredentials) {
            flash()->error('Username or password is wrong');
            $this->redirect('login');
        }

        /** @var User $user */
        auth()->persist($user);
        flash()->success('You have successfully been logged in');
        $this->redirect('/');
    }

    protected function setData(): void
    {
        $this->data = [
            'title' => config('app.page.titles')['login'],
        ];
    }
}
