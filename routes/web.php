<?php

use Illuminate\Support\Facades\Route;
use App\Services\DonkiService; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/donki', function (DonkiService $donkiService) {
    return $donkiService->getDonkiData();
});

