<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[OrderController::class,'orders'])->middleware(['verify.shopify'])->name('home');
// Route::post('/orders_post',[OrderController::class,'orders_post'])->middleware(['verify.shopify'])->name('orders_post');
Route::post('orders_post',[OrderController::class, 'orders_post'])->middleware(['verify.shopify'])->name('orders_post');
Route::post('login_save',[OrderController::class, 'login_save'])->middleware(['verify.shopify'])->name('login_save');
Route::post('default_setting',[OrderController::class, 'default_setting'])->middleware(['verify.shopify'])->name('default_setting');
Route::post('cancel_shipment',[OrderController::class, 'cancel_shipment'])->middleware(['verify.shopify'])->name('cancel_shipment');
// Route::get('/', function () {
//     return view('welcome');
// })->middleware(['verify.shopify'])->name('home');
