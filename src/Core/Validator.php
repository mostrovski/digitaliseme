<?php
namespace Digitaliseme\Core;

class Validator {
    public static function validateFileName($name, $sanitized) {
        $minlen = 5;
        $maxlen = 100;
        $output = self::validateBasics(
            $name,
            $sanitized,
            $minlen,
            $maxlen,
            FILE_NAME_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $name,
        ];
    }

    public static function validateDocTitle($title, $sanitized) {
        $minlen = 4;
        $maxlen = 100;
        $output = self::validateBasics(
            $title,
            $sanitized,
            $minlen,
            $maxlen,
            DOC_TITLE_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $title,
        ];
    }

    public static function validateCreatedDate($date) {
        $output = self::validateDate(
            $date,
            EMPTY_DATE_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $date,
        ];
    }

    public static function validateName($name, $sanitized) {
        $minlen = 2;
        $maxlen = 32;
        $output = self::validateBasics(
            $name,
            $sanitized,
            $minlen,
            $maxlen,
            NAME_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $name,
        ];
    }

    public static function validateUserName($name, $sanitized, $unique) {
        if (!$unique) return [
            'result' => false,
            'error'  => USER_NAME_UNIQUE_ERROR,
            'class'  => 'invalid',
            'show'   => $name,
        ];
        $minlen = 5;
        $maxlen = 32;
        $output = self::validateBasics(
            $name,
            $sanitized,
            $minlen,
            $maxlen,
            USER_NAME_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $name,
        ];
    }

    public static function validateUserPassword($password) {
        $minlen = 5;
        $maxlen = 32;
        $output = self::validatePassword(
            $password,
            $minlen,
            $maxlen
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $password,
        ];
    }

    public static function validateInputEmail($email) {
        $minlen = 1;
        $maxlen = 32;
        $output = self::validateEmail(
            $email,
            $minlen,
            $maxlen,
            INVALID_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $email,
        ];
    }

    public static function validateAgentName($name, $sanitized) {
        $minlen = 5;
        $maxlen = 32;
        $output = self::validateBasics(
            $name,
            $sanitized,
            $minlen,
            $maxlen,
            AGENT_NAME_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $name,
        ];
    }

    public static function validateAgentPhone($phone, $sanitized) {
        $minlen = 10;
        $maxlen = 32;
        $output = self::validateBasics(
            $phone,
            $sanitized,
            $minlen,
            $maxlen,
            INVALID_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $phone,
        ];
    }

    public static function validateStoragePlace($place, $sanitized) {
        $minlen = 1;
        $maxlen = 50;
        $output = self::validateBasics(
            $place,
            $sanitized,
            $minlen,
            $maxlen,
            STORAGE_NAME_PATTERN_ERROR
        );
        return [
            'result' => $output['result'],
            'error'  => $output['error'],
            'class'  => $output['class'],
            'show'   => $place,
        ];
    }

    public static function validateKeywords($keywords, $sanitized) {
        $minlen = 1;
        $maxlen = 200;
        $basics = self::validateBasics(
            $keywords,
            $sanitized,
            $minlen,
            $maxlen,
            KEYWORDS_PATTERN_ERROR
        );
        if (!$basics['result']) return [
            'result' => false,
            'error'  => $basics['error'],
            'class'  => $basics['class'],
            'show'   => $keywords,
        ];
        return preg_match(KEYWORDS_MATCH_PATTERN, $keywords) ?
        [
            'result' => true,
            'error'  => $basics['error'],
            'class'  => $basics['class'],
            'show'   => $keywords,
        ] :
        [
            'result' => false,
            'error'  => KEYWORDS_MATCH_ERROR,
            'class'  => 'invalid',
            'show'   => $keywords,
        ];
    }

    public static function sanitize($input, $pattern) {
        $sanitized = preg_replace($pattern, '', $input);
        $result = ($sanitized !== $input) ? true : false;
        return ['show' => $sanitized, 'result' => $result];
    }

    public static function convertName($input) {
        $hyphenPos = mb_strpos($input, '-');
        $firstCapLetter = mb_strtoupper(mb_substr($input, 0, 1));
        if (!$hyphenPos) {
            return $firstCapLetter.mb_substr(mb_strtolower($input), 1);
        } else {
            $secondCapLetter =
                mb_strtoupper(mb_substr($input, ($hyphenPos+1),1));
            return $firstCapLetter
                .mb_substr(mb_strtolower($input), 1, $hyphenPos)
                .$secondCapLetter
                .mb_substr(mb_strtolower($input), $hyphenPos+2);
        }
    }

    public static function isUnique($input, $source, $value) {
        $result = true;
        foreach($source as $element) {
            if ($input === $element->$value) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    protected static function isValidLength($input, $minLength, $maxLength) {
        $result = false;
        $error = '';
        $length = mb_strlen($input);
        if ($length === 0) {
            $error = "&#8921; this field can't be empty";
        } else if ($length > 0 && $length < $minLength) {
            $error = '&#8921; must be at least '.$minLength;
            $error .= ' characters long';
        } else if ($length > $maxLength) {
            $error = '&#8921; this input is '.$length;
            $error .= ' characters long, only '.$maxLength.' are allowed';
        } else {
            $result = true;
        }
        return ['result' => $result, 'error' => $error];
    }

    protected static function isValidPattern($sanitized, $errorMessage) {
        $result = false;
        $error = $errorMessage;
        if (!$sanitized) {
            $result = true;
            $error = '';
        }
        return ['result' => $result, 'error' => $error];
    }

    protected static function validateBasics(
        $input,
        $sanitized,
        $minlen,
        $maxlen,
        $err)
    {
        $pattern = self::isValidPattern($sanitized, $err);
        $length = self::isValidLength($input, $minlen, $maxlen);
        if (!$pattern['result']) {
            $error = $pattern['error'];
        } else if (!$length['result']) {
            $error = $length['error'];
        }
        return isset($error) ?
        ['result' => false, 'error' => $error, 'class' => 'invalid'] :
        ['result' => true,  'error' => '',     'class' => 'valid'];
    }

    protected static function validateEmail($email, $minlen, $maxlen, $err) {
        $pattern = preg_match(EMAIL_MATCH_PATTERN, $email);
        $length = self::isValidLength($email, $minlen, $maxlen);
        if (!$length['result']) {
            $error = $length['error'];
        } else if (!$pattern) {
            $error = $err;
        }
        return isset($error) ?
        ['result' => false, 'error' => $error, 'class' => 'invalid'] :
        ['result' => true,  'error' => '',     'class' => 'valid'];
    }

    protected static function validatePassword($pass, $minlen, $maxlen) {
        $length = self::isValidLength($pass, $minlen, $maxlen);
        if (!$length['result']) {
            $error = $length['error'];
        }
        return isset($error) ?
        ['result' => false, 'error' => $error, 'class' => 'invalid'] :
        ['result' => true,  'error' => '',     'class' => 'valid'];
    }

    protected static function validateDate($date, $err) {
        if (empty($date)) {
            $error = $err;
        }
        return isset($error) ?
        ['result' => false, 'error' => $error, 'class' => 'invalid'] :
        ['result' => true,  'error' => '',     'class' => 'valid'];
    }
}
?>