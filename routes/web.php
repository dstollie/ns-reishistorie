<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('reishistorie', function () {

    if($url = request('money_back_url')) {
        return response()->json(app('ns')->submitMoneyBack($url));
    } else {
        return response()->json(app('ns')->showReishistorie());
    }
});

