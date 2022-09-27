<?php
namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;
use Digitaliseme\Core\Validator;

class User {
    // Model of the user
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $userName;
    protected $password;
    protected $sanitized = [];

    public function __construct($params) {
        if (!is_array($params)) return;
        if (isset($params['id'])) {
            $user = $this->read($params['id']);
            if (!is_object($user)) return;
            $this->id = $user->id;
            $this->rirstName = $user->fname;
            $this->lastName = $user->lname;
            $this->email = $user->email;
            $this->userName = $user->uname;
            $this->password = $user->password;
        } else {
            $this->setFirstName($params['firstname'] ?? NULL);
            $this->setLastName($params['lastname'] ?? NULL);
            $this->setEmail($params['email'] ?? NULL);
            $this->setId($params['username']);
            $this->setUserName($params['username']);
            $this->setPassword($params['password']);
        }
    }

    public function logIn() {
        if (!isset($this->id) || !$this->verify($this->password)) {
            $input = [
                'username' => $this->userName,
                'password' => $this->password,
            ];
            return [
                'success' => false,
                'error'   => LOGIN_ERROR,
                'input'   => $input,
            ];
        }
        $this->addUserToSession();
        return ['success' => true, 'message' => LOGIN_OK];
    }

    public static function logOut() {
        if (!isset($_SESSION["loggedin"])) return LOGIN_NOT;
        unset($_SESSION["loggedin"]);
        unset($_SESSION["loggedinName"]);
        unset($_SESSION["loggedinID"]);
        return LOGOUT_OK;
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
        $message = $created ? SIGNUP_OK : TRY_AGAIN_ERROR;
        return [
            'valid'   => true,
            'status'  => $status,
            'message' => $message,
        ];
    }

    protected function verify($password) {
        $validPassword = $this->read($this->id)->password;
        return password_verify($password, $validPassword);
    }

    protected function convert($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function addUserToSession() {
        $user = $this->read($this->id);
        $_SESSION["loggedin"] = $user->uname;
        $_SESSION["loggedinName"] = $user->fname;
        $_SESSION["loggedinID"] = $user->id;
    }

    protected function validate() {
        $isValidFirstName = Validator::validateName(
            $this->firstName,
            $this->sanitized['firstName']
        );
        $isValidLastName = Validator::validateName(
            $this->lastName,
            $this->sanitized['lastName']
        );;
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

    protected function create() {
        $db = new Database();
        $sql = 'INSERT INTO users(fname, lname, email, uname, password) ';
        $sql .= 'values(:fname, :lname, :email, :uname, :password)';
        $created = $db->insertIntoTable(
            $sql,
            [':fname', ':lname', ':email', ':uname', ':password'],
            [
                $this->firstName,
                $this->lastName,
                $this->email,
                $this->userName,
                $this->password,
            ]
        );
        return $created ? true : false;
    }

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
        $sanitized = Validator::sanitize($firstName, NAME_PATTERN);
        $this->firstName = Validator::convertName($sanitized['show']);
        $this->sanitized['firstName'] = $sanitized['result'];
    }

    protected function setLastName($lastName) {
        $sanitized = Validator::sanitize($lastName, NAME_PATTERN);
        $this->lastName = Validator::convertName($sanitized['show']);
        $this->sanitized['lastName'] = $sanitized['result'];
    }

    protected function setEmail($email) {
        $sanitized = Validator::sanitize($email, EMAIL_SAN_PATTERN);
        $this->email = $sanitized['show'];
        $this->sanitized['email'] = $sanitized['result'];
    }

    protected function setUserName($userName) {
        $sanitized = Validator::sanitize($userName, USER_NAME_PATTERN);
        $this->userName = $sanitized['show'];
        $this->sanitized['userName'] = $sanitized['result'];
    }

    protected function setPassword($password) {
        $this->password = $password;
    }
}
?>