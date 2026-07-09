<?php

use App\Http\Controllers\ReceiptEntryController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\dummyAPI ;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('data', [dummyAPI::class, 'getData']);
// Route::get('data/{id}', [dummyAPI::class, 'getData']);
Route::post('update-delivery-status',[ReceiptEntryController::class,'updateDeliveryStatus']);

// PurchaseOrder: proxy update to internal API
Route::post('purchase-order/update-header',[PurchaseOrderController::class,'updateHeader']);
Route::post('purchase-order/update-detail',[PurchaseOrderController::class,'updateDetail']);

// Shipment
Route::post('shipment/ship-complete', [ShipmentController::class, 'shipComplete']);
