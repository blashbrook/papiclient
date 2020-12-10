<?php

use Illuminate\Support\Facades\Route;

Route::get('/patron', function () {
    return view('papiclient::patron');
});

