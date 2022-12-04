<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use function Hettiger\Honeypot\config;

Route::post(config('token_route_path'), fn () => abort(Response::HTTP_NOT_FOUND))
    ->middleware(config('token_route_middleware'))
    ->name('spa-honeypot.token');
