<?php

use Digitaliseme\Controllers\DefaultController;
use Digitaliseme\Controllers\DocumentsController;
use Digitaliseme\Controllers\LoginController;
use Digitaliseme\Controllers\LogoutController;
use Digitaliseme\Controllers\SearchController;
use Digitaliseme\Controllers\SignupController;
use Digitaliseme\Controllers\UploadsController;
use Digitaliseme\Core\Enumerations\Http\Method;
use Digitaliseme\Core\Http\Middleware\Authenticated;
use Digitaliseme\Core\Http\Middleware\Guest;
use Digitaliseme\Core\Routing\Route;

return [
    Route::define('/', Method::GET, DefaultController::class, 'index'),
    Route::define('403', Method::GET, DefaultController::class, 'index'),
    Route::define('404', Method::GET, DefaultController::class, 'index'),
    Route::define('500', Method::GET, DefaultController::class, 'index'),

    ...Route::groupMiddleware([Guest::class], [
        Route::define('login', Method::GET, LoginController::class, 'index'),
        Route::define('login', Method::POST, LoginController::class, 'init'),
        Route::define('signup', Method::GET, SignupController::class, 'index'),
        Route::define('signup', Method::POST, SignupController::class, 'init'),
    ]),

    ...Route::groupMiddleware([Authenticated::class], [
        Route::define('logout', Method::GET, LogoutController::class, 'index'),
        Route::define('uploads', Method::GET, UploadsController::class, 'index'),
        Route::define('uploads', Method::POST, UploadsController::class, 'store'),
        Route::define('uploads/create', Method::GET, UploadsController::class, 'create'),
        Route::define('uploads/{id}', Method::DELETE, UploadsController::class, 'destroy'),
        Route::define('documents', Method::GET, DocumentsController::class, 'index'),
        Route::define('documents', Method::POST, DocumentsController::class, 'store'),
        Route::define('documents/create', Method::GET, DocumentsController::class, 'create'),
        Route::define('documents/{id}', Method::GET, DocumentsController::class, 'show'),
        Route::define('documents/{id}', Method::PATCH, DocumentsController::class, 'update'),
        Route::define('documents/{id}', Method::DELETE, DocumentsController::class, 'destroy'),
        Route::define('documents/{id}/edit', Method::GET, DocumentsController::class, 'edit'),
        Route::define('documents/{id}/download', Method::GET, DocumentsController::class, 'download'),
        Route::define('search', Method::GET, SearchController::class, 'index'),
        Route::define('search', Method::POST, SearchController::class, 'find'),
    ]),
];
