<?php
Use App\Http\Controllers\ProductsController;
Use App\Http\Controllers\CategoriesController;
Use App\Http\Controllers\InformationsController;
Use App\Http\Controllers\BanksController;
Use App\Http\Controllers\PagesController;
Use App\Http\Controllers\SozlesmeController;
Use App\Http\Controllers\UserController;
Use App\Http\Controllers\SiparisController;
Use App\Http\Controllers\AdresController;
Use App\Http\Controllers\DestekController;
Use App\Http\Controllers\YorumController;
Use App\Http\Controllers\MailController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Site Bilgileri
Route::get('info',[InformationsController::class, 'index']);

//Banka Bilgieri
Route::get('bank',[BanksController::class, 'index']);

//Sabit Sayfalar
Route::get('pages',[PagesController::class, 'index']);
Route::get('pages/{id}',[PagesController::class, 'show']);

//Sözleşmeler
Route::get('sozlesme',[SozlesmeController::class, 'index']);

//Kategoriler
Route::get('categories',[CategoriesController::class, 'index']);
Route::get('categories/{id}',[CategoriesController::class, 'show']);
Route::get('categories/{id}/products',[CategoriesController::class, 'products']);

//Ürünler
Route::get('products',[ProductsController::class, 'index']);
Route::get('products/{id}',[ProductsController::class, 'show']);
Route::get('products/{id}/pictures',[ProductsController::class, 'pictures']);
Route::get('products/{id}/comments',[ProductsController::class, 'comments']);


//Hesap Bilgileri
Route::post('user',[UserController::class, 'login']);
Route::put('user',[UserController::class, 'register']);
Route::get('user/{id}',[UserController::class, 'show']);
Route::post('password/{id}',[UserController::class, 'password']);

Route::get('user/{id}/siparis',[SiparisController::class, 'siparisler']);
Route::get('user/{id}/siparis/{sip}',[SiparisController::class, 'siparis']);
Route::put('user/{id}/siparis',[SiparisController::class, 'create']);
Route::put('user/{id}/siparis/{sip}/urun',[SiparisController::class, 'create_product']);

//Kullanıcının adresleri
Route::get('user/{id}/adres',[AdresController::class, 'index']);
//Adres Detayı
Route::get('user/{id}/adres/{aid}',[AdresController::class, 'show']);
//Oluşturma
Route::put('user/{id}/adres',[AdresController::class, 'add']);
//Silme
Route::delete('user/{id}/adres/{aid}',[AdresController::class, 'delete']);
//Güncelleme
Route::post('user/{id}/adres/{aid}',[AdresController::class, 'update']);


//Destek Mesajları
Route::get('user/{id}/destek',[DestekController::class, 'index']);
//Destek Mesajları Detay
Route::get('user/{id}/destek/{did}',[DestekController::class, 'show']);
//Destek Mesajı Oluşturma
Route::put('user/{id}/destek',[DestekController::class, 'create']);
//Destek Mesajı Mesaj Gönderme
Route::post('user/{id}/destek/{did}',[DestekController::class, 'add']);

//Üye Yorumlar
Route::get('user/{id}/yorum',[YorumController::class, 'index']);
Route::get('user/{id}/yorum/{yid}',[YorumController::class, 'show']);
Route::put('user/{id}/yorum/{pid}',[YorumController::class, 'add']);


Route::get('mail/{email}/{name}',[MailController::class, 'register']);