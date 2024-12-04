<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DonkiController;
use App\Http\Controllers\NASAController;



Route::prefix('donki')->group(function () {
    Route::get('/instruments', [NASAController::class, 'getInstruments']);
    Route::get('/activity-ids', [NASAController::class, 'getActivityIDs']);
    Route::get('/instrument-usage', [NASAController::class, 'getInstrumentUsage']);
    Route::post('/instrument-percentage', [NASAController::class, 'getInstrumentPercentage']);
    Route::get('/donki', [NasaController::class, 'getDonkiData']);
});