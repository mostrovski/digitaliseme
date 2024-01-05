<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Models\User;
use Throwable;

class LoginController extends Controller
{
    public function index(): void
    {
        if (auth()->isIntact()) {
            flash()->info('You are logged in');
            $this->redirect('/');
        }

        $this->view('login');
    }

    /**
     * @throws ValidatorException
     */
    public function init(): void
    {
        if (! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            $this->redirect('404');
        }

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
}
