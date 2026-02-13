<?php

use Illuminate\Support\Facades\Route;
use Noo\LocaleRedirect\LocaleRedirectController;

Route::get('/', LocaleRedirectController::class);
