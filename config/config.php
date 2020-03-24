<?php
/****************************************************************************
 *
 *                                 DATABASE
 *
 ***************************************************************************/
const DB_HOST = 'localhost';
const DB_USER = 'homestead';
const DB_PASSWORD = 'secret';
const DB_NAME = 'digitaliseme';
const DB_CHARSET = 'utf8mb4';
/****************************************************************************
 *
 *                                 ROUTING/TEMPLATES
 *
 ***************************************************************************/
define('ROOT', dirname(dirname(__FILE__)));
const HOME = 'http://digitaliseme.test/';
const PUBLIC_ROUTES = ['signup', 'login', 'logout'];
const PRIVATE_ROUTES = ['uploads', 'documents', 'search'];
const DEFAULT_ROUTES = [
    'public'  => [
        'controller' => 'Controllers\LoginController',
        'method'     => 'index',
    ],
    'private' => [
        'controller' => 'Controllers\UploadsController',
        'method'     => 'create',
    ]
];
const SITE_INFO = [
    'name'        => 'digitalise me',
    'description' => 'cozy document archive',
    'developer'   => 'Andrei Ostrovskii',
    'greeting'    => 'aloha, wanderer',
];
const PAGE_TITLES = [
    'login'            => 'Log in',
    'signup'           => 'Sign up',
    'uploads'          => 'Files to process',
    'uploads/create'   => 'Upload file',
    'documents'        => 'Documents',
    'documents/create' => 'Work on new document',
    'documents/show'   => 'Document details',
    'documents/edit'   => 'Edit document',
    'search'           => 'Find the document',
];
const PAGE_PARTIALS = [
    'header'     => ROOT.'/views/partials/header.php',
    'navigation' => ROOT.'/views/partials/navigation.php',
    'footer'     => ROOT.'/views/partials/footer.php',
];
/****************************************************************************
 *
 *                                 FILES
 *
 ***************************************************************************/
const SUPPORTED_TYPES = ['pdf','PDF','png','PNG','jpg','JPG','jpeg','JPEG'];
const SUPPORTED_SIZE = 1048576;
/****************************************************************************
 *
 *                                 REGEX
 *
 ***************************************************************************/
define(
    'EMAIL_MATCH_PATTERN',
    '/^[a-zA-Z0-9.!$%&*+\/\=^_{\|}~-]{3,}@[a-zA-Z0-9-]{3,}(\.[a-zA-Z]{2,})$/'
);
define(
    'KEYWORDS_MATCH_PATTERN',
    '/^\s*([^,\s-]{2,}\s?[^,\s-]*){1,}\s*,\s*([^\s-]{2,}\s?[^\s-]*)*\s*$/'
);
const EMAIL_SAN_PATTERN = '/[^a-zA-Z0-9.@!$%&*+\/\=^_{\|}~-]/';
const NAME_PATTERN = '/[^a-zA-ZäöüßÄÖÜ-]/';
const USER_NAME_PATTERN = '/[^a-zA-Z0-9_-]/';
const FILE_NAME_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9_-]/';
const AGENT_NAME_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9.\s-]/';
const DOC_TITLE_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9()*,.\s-]/';
const PHONE_PATTERN = '/[^0-9+()-]/';
const STORAGE_NAME_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9,()\s-]/';
const SEARCH_ARCHIVE_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9()*,.\s-]/';
const KEYWORDS_SAN_PATTERN = '/[^a-zA-ZäöüßÄÖÜ0-9,\s-]/';
/****************************************************************************
 *
 *                                 ERROR MESSAGES
 *
 ***************************************************************************/
define(
    'AUTHENTICATION_ERROR',
    '&#8921; you have to be logged in to visit this page'
);
define(
    'USER_NAME_PATTERN_ERROR',
    '&#8921; only english alphabetic and numeric symbols, underscores, '
        .'and hyphens are allowed'
);
define(
    'NAME_PATTERN_ERROR',
    '&#8921; only alphabetic symbols and hyphens are allowed'
);
define(
    'FILE_NAME_PATTERN_ERROR',
    '&#8921; only alphabetic and numeric symbols, underscores, '
        .'and hyphens are allowed'
);
define(
    'DOC_TITLE_PATTERN_ERROR',
    '&#8921; only alphabetic and numeric symbols, spaces, round brackets, '
        .' asterisks, points, and hyphens are allowed'
);
define(
    'AGENT_NAME_PATTERN_ERROR',
    '&#8921; only alphabetic and numeric symbols, points, spaces, '
        .'and hyphens are allowed'
);
define(
    'STORAGE_NAME_PATTERN_ERROR',
    '&#8921; only alphabetic and numeric symbols, commas, round brackets, '
        .'spaces, and hyphens are allowed'
);
define(
    'KEYWORDS_PATTERN_ERROR',
    '&#8921; only alphabetic and numeric symbols, spaces, commas, '
        .'and hyphens are allowed'
);
define(
    'KEYWORDS_MATCH_ERROR',
    '&#8921; every keyword has to be at least two characters long; '
        .'within a keyphrase, only single spaces are allowed; '
        .'keywords and keyphrases have to be separated with commas'
);
define(
    'FILE_DELETE_RECORD_ERROR',
    '&#8921; failed to remove the record of this file'
);
define(
    'FILE_DELETE_AUTH_ERROR',
    '&#8921; you are not authorized to delete this file'
);
define(
    'DOCUMENT_EDIT_AUTH_ERROR',
    '&#8921; you are not authorized to edit this document'
);
define(
    'DOCUMENT_DELETE_AUTH_ERROR',
    '&#8921; you are not authorized to delete this document'
);
define(
    'DOCUMENT_UPDATE_AUTH_ERROR',
    '&#8921; you are not authorized to update this document'
);
define(
    'DOCUMENT_WORKON_AUTH_ERROR',
    '&#8921; you are not authorized to work on this document'
);
define(
    'AGENT_DB_FAILURE',
    '&#8921; failed to save document creator information'
);
define(
    'KEYWORDS_FILE_FAILURE',
    '&#8921; failed to unlink keywords from the file'
);
define(
    'FILE_DIR_UPDATE_FAILURE',
    '&#8921; failed to move file to the new directory'
);
define(
    'DOCUMENT_DELETE_RECORD_FAILURE',
    '&#8921; failed to delete document record'
);
const GENERAL_ERROR = '&#8921; something went wrong...';
const TRY_AGAIN_ERROR = '&#8921; something went wrong... Try again!';
const LOGIN_ERROR = '&#8921; user name or password is wrong';
const USER_NAME_UNIQUE_ERROR = '&#8921; you can not use this name';
const EMPTY_DATE_ERROR = '&#8921; date has to be chosen';
const NO_FILE_CHOSEN_ERROR = '&#8921; file was not chosen';
const FILE_UNIQUE_ERROR = '&#8921; this file was already uploaded';
const NO_FILE_ERROR = '&#8921; this file does not exist';
const NO_DOCUMENT_ERROR = '&#8921; this document does not exist';
const FILE_DELETE_ERROR = '&#8921; failed to delete this file';
const FILE_TYPE_ERROR = '&#8921; this file type is not supported';
const FILE_SIZE_ERROR = '&#8921; this file is larger than 1MB';
const FILE_EMPTY_ERROR = '&#8921; file can not be empty';
const INVALID_ERROR = '&#8921; this input is invalid';
const EMPTY_ERROR = '&#8921; can not be empty';
const STORAGE_DB_FAILURE = '&#8921; failed to save physical storage';
const FILE_DB_FAILURE = '&#8921; failed to save file information';
const KEYWORDS_DB_FAILURE = '&#8921; failed to save keywords';
const DOCUMENT_DB_FAILURE = '&#8921; failed to save document info';
const FILE_TO_ARCHIVE_FAILURE = '&#8921; failed to move file to archive';
const DOWNLOAD_FAILURE = '&#8921; failed to download file';
const TITLE_UPDATE_FAILURE = '&#8921; failed to update document title';
const DATE_UPDATE_FAILURE = '&#8921; failed to update date of creation';
const FILE_NAME_UPDATE_FAILURE = '&#8921; failed to update file name';
const FILE_PATH_UPDATE_FAILURE = '&#8921; failed to update file path';
const DOCUMENT_UPDATE_FAILURE = '&#8921; failed to update document details';
const DOCUMENT_DELETE_FILE_FAILURE = '&#8921; failed to delete document file';
const NO_INPUT_ERROR = '&#8921; you have not specified search criteria';
/****************************************************************************
 *
 *                                 INFO MESSAGES
 *
 ***************************************************************************/
define(
    'SIGNUP_OK',
    '&#8921; kudos, you can now log in with your user name and password'
);
define(
    'NO_UPLOADS',
    '&#8921; there is nothing to work on, upload new file <a href="'
        .HOME.'uploads/create">here</a>'
);
define(
    'NO_DOCUMENTS',
    '&#8921; the archive is empty, upload new file <a href="'
        .HOME.'uploads/create">here</a>'
);
define(
    'NO_SEARCH_RESULTS',
    '&#8921; there are no documents that match your search request'
);
const LOGIN_OK = '&#8921; you have successfully been logged in';
const LOGIN_ALREADY = '&#8921; you are logged in';
const LOGIN_NOT = '&#8921; you are not logged in';
const LOGOUT_OK = '&#8921; you have successfully been logged out';
const SIGNUP_ALREADY = '&#8921; you have already signed up';
const UPLOAD_OK = '&#8921; file was successfully uploaded';
const DOWNLOAD_OK = '&#8921; file was successfully downloaded';
const UPLOAD_DELETE_OK = '&#8921; file was successfully deleted';
const NEW_DOC_OK = '&#8921; document was successfully saved';
const UPDATE_DOC_OK = '&#8921; document details were successfully updated';
const DELETE_DOC_OK = '&#8921; document was successfully deleted';
?>