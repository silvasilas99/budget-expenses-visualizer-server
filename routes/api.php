<?php

use App\Domain\Exchange\ExchangeSearchController;
use App\Domain\Exchange\ExchangeCrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'budget_exchanges',
], function () {
    Route::get('/find', [ExchangeSearchController::class, 'index'])->name('budget_exchanges.find');
    Route::get('/show/{counter}/by/{grouper}', [ExchangeSearchController::class, 'getDataByAggroupment'])->name('budget_exchanges.getDataByAggroupment');
});
