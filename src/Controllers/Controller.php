<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View as ViewResponse;
use Digitaliseme\Core\Session\CSRF;
use Digitaliseme\Core\Session\Errors;
use Digitaliseme\Core\Validation\Validator;
use Digitaliseme\Core\View\View;

abstract class Controller
{
    protected function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function hasValidToken(): bool
    {
        $token = $_POST['token'] ?? '';

        return ! empty($token) && (CSRF::handler()->token() === $token);
    }

    /**
     * @throws ValidatorException
     */
    protected function validate(array $params, array $rules, array $messages): Validator
    {
        return (new Validator($params, $rules, $messages))->validate();
    }

    protected function view(string $view, array $data = [], int $statusCode = 200): ViewResponse
    {
        return (new ViewResponse)
            ->setStatusCode($statusCode)
            ->setContent(View::make($view, $data)->render());
    }

    protected function redirect($url, array $data = []): Redirect
    {
        return redirectResponse($url, $data);
    }

    protected function withErrors(array $errors): static
    {
        Errors::handler()->set($errors);

        return $this;
    }
}
