# Digitalise Me :: document archive
*the MVC application*

## The task

A client deals with lots of physical documents. The application should store
digital copies of that documents as well as the following information for
each document:

- title;
- date of creation;
- name, email, and phone number of the document creator;
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

> Requested url => Controller => Model => Controller => View

### The structure of the application:

- *app*
    - archive   => storage for documents
    - uploads   => temporal storage for uploads
    - downloads => temporal storage for downloads
    - public    => styles and graphics
- *config*
    - `autoload.php`
    - `config.php`
- *core*
    - `Database.php`
    - `Page.php`
    - `Helper.php`
    - `Validator.php`
- *controllers*
    - `Controller.php`
    - `DefaultController.php`
    - `DocumentsController.php`
    - `LoginController.php`
    - `LogoutController.php`
    - `SignupController.php`
    - `SearchController.php`
    - `UploadsController.php`
- *models*
    - `User.php`
    - `File.php`
    - `RawFile.php`
    - `UploadedFile.php`
    - `DocumentFile.php`
    - `Document.php`
    - `RawDocument.php`
    - `ArchiveDocument.php`
    - `DocumentAgent.php`
    - `DocumentType.php`
    - `DocumentStorage.php`
    - `DocumentKeyword.php`
    - `SearchDocument.php`
- *views*
    - partials  => header, navigation, footer
    - templates => specific views
- `.htaccess`
- `index.php`

### What's in code:

- namespaces;
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

## How to run it *(recommended setup)*

 1. Download and extract the repository.
 2. Make sure **XAMPP** package is installed on your computer. Otherwise,
    download and install it from [here](https://www.apachefriends.org/download.html).
 3. After step 2, you should have the *xampp* directory (or similar, depending
    on your OS) with the *htdocs* subdirectory inside. If you can't locate it,
    check XAMPP documentation (accessible from XAMPP download page) for help.
 4. You may want to delete everything from the *htdocs* before moving on.
 5. Move the folder from step 1 to the *htdocs*; rename it to *digitaliseme*.
 6. Open *XAMPP Control Panel*.
 7. Start *Apache* and *MySQL* servers.
 8. In your browser, go to *localhost/phpmyadmin*.
 9. Create new database *digitaliseme* with the *utf8mb4_unicode_ci* collation.
10. Make sure the database from step 9 is selected and go to the `Import` tab.
11. Choose `digitaliseme.sql`, which should be found in the application directory,
    under */config/db* (see step 5), and press `Go`.
12. After step 11, your database should contain all the necessary tables.
    Compare it to `digitaliseme_schema.png` from */config/db* (see step 11)
    to be sure.
13. Make sure to modify `.htaccess` in the root of your application directory
    and/or `config.php` under */config/* to reflect changes in these cases:
    - you renamed the application folder from step 1 to anything other than
      *digitaliseme*;
    - you moved the contents of the application folder to *htdocs* instead
      of moving the whole folder on step 5;
    - you created the database with the name other than *digitaliseme* and/or
      charset other than *utf8mb4* on step 9;
    - you changed default settings (host, user, password) for your database;
    Skip this step if none of the above applies.
14. If *Apache* and *MySQL* are running (see step 7), you should be able to
    access and use the application.
15. In your browser, open *localhost/digitaliseme* or *localhost/[your_name]*
    (see step 13).

## Acknowledgments

[Brad Traversy](https://github.com/bradtraversy) inspired the Database and Page classes;
[Sergii Makagon](https://github.com/smakagon) provided invaluable advice and motivation.