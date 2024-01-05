<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Session\CSRF;
use Digitaliseme\Core\Session\Errors;
use Digitaliseme\Core\Validation\Validator;

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

    protected function redirect($url): void
    {
        redirect($url);
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);

        ob_start();
        include app()->root()."/views/$view.php";
        $content = ob_get_clean();

        ob_start();
        include app()->root().'/views/templates/master.php';
        $template = ob_get_clean();

        echo str_replace('___\content/___', $content, subject: $template);
    }

    protected function withErrors(array $errors): static
    {
        Errors::handler()->set($errors);

        return $this;
    }
}
