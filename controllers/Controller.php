<?php
namespace Controllers;

abstract class Controller {
    // Base class for controllers
    protected function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isValidToken($token) {
        if (!isset($_SESSION['token'])) return false;
        return $_SESSION['token'] === $token;
    }

    protected function generateToken() {
        $token = hash('sha256', uniqid());
        $_SESSION['token'] = $token;
        return $token;
    }

    protected function destroyToken() {
        unset($_SESSION['token']);
    }

    protected function view($template, $data = []) {
        require_once ROOT.'/views/partials/header.php';
        require_once ROOT.'/views/partials/navigation.php';
        require_once ROOT.'/views/templates/'.$template.'.php';
        require_once ROOT.'/views/partials/footer.php';
    }
}
?>