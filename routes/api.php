<?php
Use App\Http\Controllers\ProductsController;
Use App\Http\Controllers\CategoriesController;
Use App\Http\Controllers\InformationsController;
Use App\Http\Controllers\BanksController;
Use App\Http\Controllers\PagesController;
Use App\Http\Controllers\SozlesmeController;
Use App\Http\Controllers\UserController;
Use App\Http\Controllers\SearchController;
Use App\Http\Controllers\SiparisController;
Use App\Http\Controllers\AdresController;
Use App\Http\Controllers\DestekController;
Use App\Http\Controllers\YorumController;
Use App\Http\Controllers\CampaignController;
Use App\Http\Controllers\MailController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Site Bilgileri
Route::middleware('auth:api_token')->get('info',[InformationsController::class, 'index']);

//Banka Bilgieri
Route::middleware('auth:api_token')->get('bank',[BanksController::class, 'index']);

//Sabit Sayfalar
Route::middleware('auth:api_token')->get('pages',[PagesController::class, 'index']);
Route::middleware('auth:api_token')->get('pages/{id}',[PagesController::class, 'show']);

//Sözleşmeler
Route::middleware('auth:api_token')->get('sozlesme',[SozlesmeController::class, 'index']);


Route::middleware('auth:api_token')->get('search',[SearchController::class, 'index']);


Route::middleware('auth:api_token')->get('kampanya',[CampaignController::class, 'index']);
Route::middleware('auth:api_token')->get('kampanya/{id}',[CampaignController::class, 'show']);

//Kategoriler
Route::middleware('auth:api_token')->get('categories',[CategoriesController::class, 'index']);
Route::middleware('auth:api_token')->get('categories/{id}',[CategoriesController::class, 'show']);
Route::middleware('auth:api_token')->get('categories/{id}/products',[CategoriesController::class, 'products']);

//Ürünler
Route::middleware('auth:api_token')->get('products',[ProductsController::class, 'index']);

Route::middleware('auth:api_token')->get('products/discounts',[ProductsController::class, 'discounts']);
Route::middleware('auth:api_token')->get('products/{id}',[ProductsController::class, 'show']);
Route::middleware('auth:api_token')->get('products/{id}/pictures',[ProductsController::class, 'pictures']);
Route::middleware('auth:api_token')->get('products/{id}/comments',[ProductsController::class, 'comments']);
Route::middleware('auth:api_token')->get('products/{id}/comments/{cid}',[ProductsController::class, 'comment']);
Route::middleware('auth:api_token')->post('products/{id}/comments/{cid}',[ProductsController::class, 'commentpoint']);


//Hesap Bilgileri
Route::middleware('auth:api_token')->get('user/{id}', [UserController::class, 'show']);
Route::middleware('auth:api_token')->post('user/login',[UserController::class, 'login']);
Route::middleware('auth:api_token')->post('user/register', [UserController::class, 'register']);
Route::middleware('auth:api_token')->post('user/{id}/password', [UserController::class, 'password']);

Route::middleware('auth:api_token')->get('user/{id}/siparis',[SiparisController::class, 'siparisler']);
Route::middleware('auth:api_token')->get('user/{id}/siparis/{sip}',[SiparisController::class, 'siparis']);
Route::middleware('auth:api_token')->put('user/{id}/siparis',[SiparisController::class, 'create']);
Route::middleware('auth:api_token')->put('user/{id}/siparis/{sip}/urun',[SiparisController::class, 'create_product']);

//Kullanıcının adresleri
Route::middleware('auth:api_token')->get('user/{id}/adres',[AdresController::class, 'index']);
//Adres Detayı
Route::middleware('auth:api_token')->get('user/{id}/adres/{aid}',[AdresController::class, 'show']);
//Oluşturma
Route::middleware('auth:api_token')->put('user/{id}/adres',[AdresController::class, 'add']);
//Silme
Route::middleware('auth:api_token')->delete('user/{id}/adres/{aid}',[AdresController::class, 'delete']);
//Güncelleme
Route::middleware('auth:api_token')->post('user/{id}/adres/{aid}',[AdresController::class, 'update']);


//Destek Mesajları
Route::middleware('auth:api_token')->get('user/{id}/destek',[DestekController::class, 'index']);
//Destek Mesajları Detay
Route::middleware('auth:api_token')->get('user/{id}/destek/{did}',[DestekController::class, 'show']);
//Destek Mesajı Oluşturma
Route::middleware('auth:api_token')->put('user/{id}/destek',[DestekController::class, 'create']);
//Destek Mesajı Mesaj Gönderme
Route::middleware('auth:api_token')->post('user/{id}/destek/{did}',[DestekController::class, 'add']);

//Üye Yorumlar
Route::middleware('auth:api_token')->get('user/{id}/yorum',[YorumController::class, 'index']);
Route::middleware('auth:api_token')->get('user/{id}/yorum/{yid}',[YorumController::class, 'show']);
Route::middleware('auth:api_token')->put('user/{id}/yorum/{pid}',[YorumController::class, 'add']);


Route::middleware('auth:api_token')->get('mail/{email}/{name}',[MailController::class, 'register']);