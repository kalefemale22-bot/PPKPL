<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/monitoring');
});

Route::get('/monitoring', function () {
    return view('monitoring');
});
