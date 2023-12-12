<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;
use Throwable;

class SignupController extends Controller
{
    protected array $data;

    public function __construct()
    {
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

        $validator = $this->validate($_POST, [
            'first_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ-]+$/'],
            'last_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ-]+$/'],
            'email' => ['required', 'email'],
            'username' => ['required', 'min:5', 'max:32', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'password' => ['required', 'min:8', 'max:32'],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',

            'first_name.min' => 'First name should be at least 2 characters long.',
            'first_name.max' => 'First name should be no longer than 32 characters.',
            'last_name.min' => 'Last name should be at least 2 characters long.',
            'last_name.max' => 'Last name should be no longer than 32 characters.',
            'username.min' => 'Username should be at least 5 characters long.',
            'username.max' => 'Username should be no longer than 32 characters.',
            'password.min' => 'Password should be at least 8 characters long.',
            'password.max' => 'Password should be no longer than 32 characters.',

            'email.email' => 'The email is invalid.',
            'first_name.regex' => 'Only alphabetic symbols and hyphens are allowed.',
            'last_name.regex' => 'Only alphabetic symbols and hyphens are allowed.',
            'username.regex' => 'Only english alphabetic and numeric symbols, underscores, and hyphens are allowed.',
            'username.unique' => 'This username is already taken.',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->getErrors())->redirect('signup');
        }

        try {
            (new User)->create($validator->getValidated());
            flash()->success(config('app.messages.info.SIGNUP_OK'));
            $this->redirect('login');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error(config('app.messages.error.TRY_AGAIN_ERROR'));
            $this->redirect('signup');
        }
    }

    protected function setData(): void
    {
        $this->data = [
            'title' => config('app.page.titles')['signup'],
        ];
    }
}
