# Digitalise Me :: cozy document archive

## A demo PHP project showcasing building the MVC application from scratch.

### Minimum requirements

A client deals with lots of physical documents. The application should store
digital copies of that documents as well as the following information for
each document:

- title;
- date of issue;
- name, email, and phone number of the document issuer;
- type (category) of the document;
- information on where the original (physical) version is stored;
- keywords.

The application should provide the possibility to *create, read, update,
delete*, and *search* for document records, as well as to *upload* and *download*
digital copies of the documents.

New users should be able to *sign up*. Registered users should be able to
access the application using their usernames and passwords.

*Authenticated* users should be able to upload and download files, create, read,
and search for document records. They should be *authorized* to update and
delete only the records they created.

### Approach

The original implementation of this project dates back to 2018 - it happened to be my very first PHP project.
Since then, my skills and preferences naturally evolved, so I decided to make a rebuild, changing a lot but preserving the original spirit:

- the project should be designed as the Model-view-controller (MVC) application;
- the productive part of the application should have zero dependencies on any third-party libraries or frameworks;
- for the backend architecture is the main focus, styles are irrelevant.

Here are some of the changes that come with the rebuild:

- project structure;
- approach to the configuration;
- application bootstrap;
- request cycle;
- database abstraction;
- routing;
- messaging between the models and controllers;
- view rendering.

For "then and now" kind of thing, the old code is still available in the [xampp](https://github.com/mostrovski/digitaliseme/tree/xampp) branch.

### How to run it

1. Make sure you have [DDEV](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/) installed.
2. Clone, or download and extract the repository.
3. Change to the root of the project.
4. Run the following commands:
   ```bash
   ddev start
   ddev import-db --file=_db/dump.sql
   ddev storage-link
   ```
5. Head over to the https://digitaliseme.ddev.site in your browser.

### Useful commands

```bash
# Start containers (will also trigger the composer install)
ddev start

# If you make changes to the .ddev/config.yaml
ddev restart

# Import the database from the dump file
ddev import-db --file=<filename>.sql

# Open the application in the browser
ddev launch

# Open the database GUI in the browser
ddev phpmyadmin

# Symlink the storage directory to the public directory
ddev storage-link

# Lint the code
ddev exec './vendor/bin/php-cs-fixer fix'

# Stop containers
ddev stop
```

### Kudos
- [Brad Traversy](https://github.com/bradtraversy) inspired original [Database](https://github.com/mostrovski/digitaliseme/blob/xampp/core/Database.php) and [Page](https://github.com/mostrovski/digitaliseme/blob/xampp/core/Page.php) classes;
- [Sergii Makagon](https://github.com/smakagon) provided invaluable advice and motivation.
