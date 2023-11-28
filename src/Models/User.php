<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;
use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Meta\Setter;
use Digitaliseme\Core\ORM\Model;
use Digitaliseme\Core\Validator;

class User extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $username;
    #[ModelAttribute]
    public string $first_name;
    #[ModelAttribute]
    public string $last_name;
    #[ModelAttribute]
    public string $email;
    #[
        ModelAttribute(protectedOnUpdate: true),
        Setter(methodName: 'convert')
    ]
    public string $password;

    public function signUp() {
        $validated = $this->validate();
        if (!$validated['success']) return [
            'valid' => false,
            'input' => $validated['data'],
        ];
        $this->password = $this->convert($this->password);
        $created = $this->create();
        $status = $created ? 'okay' : 'error';
        $message = $created ? config('app.messages.info.SIGNUP_OK') : config('app.messages.error.TRY_AGAIN_ERROR');
        return [
            'valid'   => true,
            'status'  => $status,
            'message' => $message,
        ];
    }

    protected function convert(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function validate() {
        $isValidFirstName = Validator::validateName(
            $this->firstName,
            $this->sanitized['firstName']
        );
        $isValidLastName = Validator::validateName(
            $this->lastName,
            $this->sanitized['lastName']
        );
        $isValidEmail = Validator::validateInputEmail($this->email);
        $isValidUserName = Validator::validateUserName(
            $this->userName,
            $this->sanitized['userName'],
            !isset($this->id)
        );
        $isValidPassword = Validator::validateUserPassword($this->password);
        $success =
            $isValidFirstName['result'] &&
            $isValidLastName['result'] &&
            $isValidEmail['result'] &&
            $isValidUserName['result'] &&
            $isValidPassword['result'];
        $data = [
            'first_name' => $isValidFirstName,
            'last_name'  => $isValidLastName,
            'email'     => $isValidEmail,
            'username'  => $isValidUserName,
            'password'  => $isValidPassword,
        ];
        return [
            'success' => $success,
            'data'    => $data,
        ];
    }

//    protected function create() {
//        $db = new Database();
//        $sql = 'INSERT INTO users(first_name, last_name, email, username, password) ';
//        $sql .= 'values(:first_name, :last_name, :email, :username, :password)';
//        $created = $db->insertIntoTable(
//            $sql,
//            [':first_name', ':last_name', ':email', ':username', ':password'],
//            [
//                $this->firstName,
//                $this->lastName,
//                $this->email,
//                $this->userName,
//                $this->password,
//            ]
//        );
//        return $created ? true : false;
//    }

    protected function read($id) {
        $db = new Database();
        $sql = 'SELECT * FROM users WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function setId($userName) {
        $db = new Database();
        $sql = 'SELECT id FROM users WHERE username = :username';
        $user = $db->fetchSingleRow($sql, ':username', $userName);
        $this->id = is_object($user) ? $user->id : NULL;
    }

    protected function setFirstName($firstName) {
        if (empty($firstName)) {
            return;
        }
        $sanitized = Validator::sanitize($firstName, config('app.regex.name'));
        $this->firstName = Validator::convertName($sanitized['show']);
        $this->sanitized['firstName'] = $sanitized['result'];
    }

    protected function setLastName($lastName) {
        if (empty($lastName)) {
            return;
        }
        $sanitized = Validator::sanitize($lastName, config('app.regex.name'));
        $this->lastName = Validator::convertName($sanitized['show']);
        $this->sanitized['lastName'] = $sanitized['result'];
    }

    protected function setEmail($email) {
        if (empty($email)) {
            return;
        }
        $sanitized = Validator::sanitize($email, config('app.regex.email_san'));
        $this->email = $sanitized['show'];
        $this->sanitized['email'] = $sanitized['result'];
    }

    protected function setUserName($userName) {
        $sanitized = Validator::sanitize($userName, config('app.regex.user_name'));
        $this->userName = $sanitized['show'];
        $this->sanitized['userName'] = $sanitized['result'];
    }

    protected function setPassword($password) {
        $this->password = $password;
    }
}
