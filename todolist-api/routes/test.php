<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test-simple', function () {
    return response()->json(['message' => 'API funcionando!']);
});
