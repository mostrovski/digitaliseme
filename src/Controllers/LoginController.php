<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;
use Digitaliseme\Models\User;
use Throwable;

class LoginController extends Controller
{
    public function index(): Redirect|View
    {
        if (auth()->isIntact()) {
            flash()->info('You are logged in');
            return $this->redirect('/');
        }

        return $this->view('login');
    }

    /**
     * @throws ValidatorException
     */
    public function init(): Redirect
    {
        if (! $this->isPostRequest()) {
            return $this->redirect('404');
        }

        $validator = $this->validate($this->request()->data(), [
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return $this->withErrors($validator->getErrors())->redirect('login');
        }

        $params = $validator->getValidated();
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
            return $this->redirect('login');
        }

        /** @var User $user */
        auth()->persist($user);
        flash()->success('You have successfully been logged in');
        return $this->redirect('/');
    }
}
