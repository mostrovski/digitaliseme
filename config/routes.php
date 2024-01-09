<?php

use Digitaliseme\Controllers\DefaultController;
use Digitaliseme\Controllers\DocumentsController;
use Digitaliseme\Controllers\LoginController;
use Digitaliseme\Controllers\LogoutController;
use Digitaliseme\Controllers\SearchController;
use Digitaliseme\Controllers\SignupController;
use Digitaliseme\Controllers\UploadsController;
use Digitaliseme\Core\Routing\Route;

return [
    Route::define('/', 'GET', DefaultController::class, 'index'),
    Route::define('403', 'GET', DefaultController::class, 'index'),
    Route::define('404', 'GET', DefaultController::class, 'index'),
    Route::define('500', 'GET', DefaultController::class, 'index'),
    Route::define('login', 'GET', LoginController::class, 'index'),
    Route::define('login', 'POST', LoginController::class, 'init'),
    Route::define('logout', 'GET', LogoutController::class, 'index'),
    Route::define('signup', 'GET', SignupController::class, 'index'),
    Route::define('signup', 'POST', SignupController::class, 'init'),
    // Authenticated routes TODO: groups
    Route::define('uploads', 'GET', UploadsController::class, 'index', ['auth']),
    Route::define('uploads', 'POST', UploadsController::class, 'store', ['auth']),
    Route::define('uploads/create', 'GET', UploadsController::class, 'create', ['auth']),
    Route::define('uploads/{id}', 'DELETE', UploadsController::class, 'destroy', ['auth']),
    Route::define('documents', 'GET', DocumentsController::class, 'index', ['auth']),
    Route::define('documents', 'POST', DocumentsController::class, 'store', ['auth']),
    Route::define('documents/create', 'GET', DocumentsController::class, 'create', ['auth']),
    Route::define('documents/{id}', 'GET', DocumentsController::class, 'show', ['auth']),
    Route::define('documents/{id}', 'PATCH', DocumentsController::class, 'update', ['auth']),
    Route::define('documents/{id}', 'DELETE', DocumentsController::class, 'destroy', ['auth']),
    Route::define('documents/{id}/edit', 'GET', DocumentsController::class, 'edit', ['auth']),
    Route::define('documents/{id}/download', 'GET', DocumentsController::class, 'download', ['auth']),
    Route::define('search', 'GET', SearchController::class, 'index', ['auth']),
    Route::define('search', 'POST', SearchController::class, 'find', ['auth']),
];
