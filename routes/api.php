<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoicingController;

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


Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World!']);
});



// FBR API routes
Route::middleware('web')->group(function () {
    Route::post('api/fbr/registration-type', [InvoicingController::class, 'getRegistrationType'])->name('api.fbr.registration-type');
    Route::post('api/fbr/sale-type-to-rate', [InvoicingController::class, 'getSaleTypeToRate'])->name('api.fbr.sale-type-to-rate');
    Route::post('api/fbr/sro-schedule', [InvoicingController::class, 'getSroSchedule'])->name('api.fbr.sro-schedule');
    Route::post('api/fbr/sro-item', [InvoicingController::class, 'getSroItem'])->name('api.fbr.sro-item');
    Route::get('api/fbr/item-description-codes/search', [InvoicingController::class, 'searchItemDescriptionCodes'])->name('api.fbr.item-description-codes.search');
    Route::post('api/fbr/uom-by-hs-code', [InvoicingController::class, 'getUomByHsCode'])->name('api.fbr.uom-by-hs-code');
    
    // Buyer API routes
    Route::get('/api/buyers', [InvoicingController::class, 'getBuyers'])->name('api.buyers.index');
    Route::get('/api/buyers/search', [InvoicingController::class, 'searchBuyersByNtn'])->name('api.buyers.search');
    Route::get('/api/buyers/{id}', [InvoicingController::class, 'getBuyerById'])->name('api.buyers.show');
    Route::put('/api/buyers/{id}', [InvoicingController::class, 'updateBuyer'])->name('api.buyers.update');
    Route::delete('/api/buyers/{id}', [InvoicingController::class, 'deleteBuyer'])->name('api.buyers.delete');
});
