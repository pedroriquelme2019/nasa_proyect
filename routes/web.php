<?php

use Illuminate\Support\Facades\Route;
use App\Services\DonkiService; // Importa el servicio DonkiService

// Ruta para la página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Ruta para obtener datos desde la API de NASA usando DonkiService
Route::get('/donki', function (DonkiService $donkiService) {
    return $donkiService->getDonkiData();
});

