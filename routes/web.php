<?php

use Illuminate\Support\Facades\Route;
use Noo\LocaleRedirect\LocaleRedirectMiddleware;
use Statamic\Http\Controllers\FrontendController;

Route::get('/', [FrontendController::class, 'index'])
    ->middleware(LocaleRedirectMiddleware::class);
