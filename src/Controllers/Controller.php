<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Helper;
use Digitaliseme\Core\Validation\Validator;

abstract class Controller
{
    protected function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isValidToken(string $token): bool
    {
        if (! isset($_SESSION['token'])) {
            return false;
        }

        return $_SESSION['token'] === $token;
    }

    protected function generateToken(): string
    {
        $token = hash('sha256', uniqid());
        $_SESSION['token'] = $token;

        return $token;
    }

    protected function destroyToken(): void
    {
        unset($_SESSION['token']);
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
        Helper::redirect($url);
    }

    protected function view($template, $data = []): void
    {
        require_once app()->root().'/views/partials/header.php';
        require_once app()->root().'/views/partials/navigation.php';
        require_once app()->root().'/views/templates/'.$template.'.php';
        require_once app()->root().'/views/partials/footer.php';
    }

    protected function withErrors(array $errors): static
    {
        $_SESSION['errors'] = $errors;

        return $this;
    }
}
