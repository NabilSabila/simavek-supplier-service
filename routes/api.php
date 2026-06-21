<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;

Route::apiResource('suppliers', SupplierController::class);

Route::get('/purchase-orders',[PurchaseOrderController::class,'index']);
Route::post('/purchase-orders',[PurchaseOrderController::class,'store']);
Route::get('/purchase-orders/{id}',[PurchaseOrderController::class,'show']);
Route::put('/purchase-orders/{id}',[PurchaseOrderController::class,'update']);
