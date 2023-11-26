<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;
use Digitaliseme\Core\Validator;
use Digitaliseme\Exceptions\DatabaseException;

class User extends Model{
    // Model of the user
//    protected $id;
//    protected $firstName;
//    protected $lastName;
//    protected $email;
//    protected $userName;
//    protected $password;
//    protected $sanitized = [];

//    public function __construct($params) {
//        parent::__construct();
//        if (!is_array($params)) {
//            return;
//        }
//        if (isset($params['id'])) {
//            $user = $this->read($params['id']);
//            if (!is_object($user)) return;
//            $this->id = $user->id;
//            $this->firstName = $user->fname;
//            $this->lastName = $user->lname;
//            $this->email = $user->email;
//            $this->userName = $user->uname;
//            $this->password = $user->password;
//        } else {
//            $this->setFirstName($params['firstname'] ?? NULL);
//            $this->setLastName($params['lastname'] ?? NULL);
//            $this->setEmail($params['email'] ?? NULL);
//            $this->setId($params['username'] ?? NULL);
//            $this->setUserName($params['username'] ?? NULL);
//            $this->setPassword($params['password']?? NULL);
//        }
//    }

    /**
     * {@inheritDoc}
     * @return self
     *
     * @throws DatabaseException
     */
    public function create(array $params): static
    {
        $id = $this->query()->create($params);
        return $this->query()->where('id', '=', $id)->first();
    }

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

    protected function convert($password) {
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
            'firstname' => $isValidFirstName,
            'lastname'  => $isValidLastName,
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
//        $sql = 'INSERT INTO users(fname, lname, email, uname, password) ';
//        $sql .= 'values(:fname, :lname, :email, :uname, :password)';
//        $created = $db->insertIntoTable(
//            $sql,
//            [':fname', ':lname', ':email', ':uname', ':password'],
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
        $sql = 'SELECT id FROM users WHERE uname = :uname';
        $user = $db->fetchSingleRow($sql, ':uname', $userName);
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
