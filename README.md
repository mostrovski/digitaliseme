# Digitalise Me :: cozy document archive
*the MVC application*

- [The task](#the-task)
- [Implementation](#implementation)
    - [The structure](#the-structure-of-the-application)
    - [What's in code](#whats-in-code)
- [Dependencies](#dependencies-built-with)
- [How to run it](#how-to-run-it)
- [Acknowledgments](#acknowledgments)

## The task

A client deals with lots of physical documents. The application should store
digital copies of that documents as well as the following information for
each document:

- title;
- date of creation;
- name, email, and phone number of the document agent (creator);
- type (category) of the document;
- information on where the physical version is stored;
- keywords.

The application should provide the possibility to *create, read, update,
delete*, and *search* for document records, as well as to *upload* and *download*
digital copies of the documents.

New users should be able to *sign up*. Registered users should be able to
access the application using their usernames and passwords.

*Authenticated* users should be able to upload and download files, create, read,
and search for document records. They should be *authorized* to update and
delete only the records they created.

## Implementation

The application attempts to follow the **Model-View-Controller** pattern.
A flow from the request to the view looks as follows:

> Requested url -> Controller -> [Model -> Controller] -> View

### The structure of the application:

- **app**
    - public
        - *css*
            - `main.css`
        - *img*
            - `404.png`
            - `empty.phg`
            - `error.png`
            - `logo.png`
    - archive (storage for documents arranged by type)
        - *bill*
        - *contract*
        - *information*
        - *invoice*
        - *notice*
        - *proposal*
        - *reminder*
        - *report*
        - *request*
        - *others*
    - uploads
    - downloads
- **config**
    - `autoload.php`
    - `config.php`
    - db (can be deleted after [setup](#how-to-run-it))
        - `digitaliseme.sql`
        - `digitaliseme_schema.png`
- **core**
    - `Database.php`
    - `Page.php`
    - `Helper.php`
    - `Validator.php`
- **controllers**
    - `Controller.php` (abstract)
    - `DefaultController.php`
    - `DocumentsController.php`
    - `LoginController.php`
    - `LogoutController.php`
    - `SignupController.php`
    - `SearchController.php`
    - `UploadsController.php`
- **models**
    - `User.php`
    - `File.php` (abstract)
    - `RawFile.php`
    - `UploadedFile.php`
    - `DocumentFile.php`
    - `Document.php` (abstract)
    - `RawDocument.php`
    - `ArchiveDocument.php`
    - `SearchDocument.php`
    - `DocumentAgent.php`
    - `DocumentType.php`
    - `DocumentStorage.php`
    - `DocumentKeyword.php`
- **views**
    - partials
        - `header.php`
        - `navigation.php`
        - `footer.php`
    - templates
        - *uploads*
            - `index.php`
            - `create.php`
        - *documents*
            - `index.php`
            - `show.php`
            - `create.php`
            - `edit.php`
        - *search*
            - `index.php`
        - `login.php`
        - `signup.php`
        - `404.php`
- `.htaccess`
- `index.php`

### What's in code:

- namespaces;
- autoloading classes;
- abstract classes;
- static methods;
- null coalescing operators;
- sessions;
- basic CSRF protection;
- regular expressions.

## Dependencies (built with)

- [PHP 7](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [HTML](https://www.w3.org/html/)
- [CSS](https://www.w3.org/Style/CSS/)

## How to run it
*(Homestead)*

 @TODO

## Acknowledgments

- [Brad Traversy](https://github.com/bradtraversy) inspired the Database and Page classes;
- [Sergii Makagon](https://github.com/smakagon) provided invaluable advice and motivation.