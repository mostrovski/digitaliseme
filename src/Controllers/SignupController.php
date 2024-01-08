<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;
use Digitaliseme\Models\User;
use Throwable;

class SignupController extends Controller
{
    public function index(): Redirect|View
    {
        if (auth()->isIntact()) {
            flash()->info('You have already signed up');
            return $this->redirect('/');
        }

        return $this->view('signup');
    }

    /**
     * @throws ValidatorException
     */
    public function init(): Redirect
    {
        if (! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            return $this->redirect('404');
        }

        $validator = $this->validate($_POST, $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return $this->withErrors($validator->getErrors())->redirect('signup');
        }

        try {
            User::go()->create($validator->getValidated());
            flash()->success('Kudos, you can now log in with your username and password');
            return $this->redirect('login');
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            flash()->error('Something went wrong... Try again!');
            return $this->redirect('signup');
        }
    }

    protected function validationRules(): array
    {
        return [
            'first_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ-]+$/'],
            'last_name' => ['required', 'min:2', 'max:32', 'regex:/^[a-zA-ZäöüßÄÖÜ-]+$/'],
            'email' => ['required', 'email'],
            'username' => ['required', 'min:5', 'max:32', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'password' => ['required', 'min:8', 'max:32'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
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
        ];
    }
}
