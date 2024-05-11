<?php

use App\Helpers\Constants;
use App\Http\Controllers\OrchidController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Middleware\Pages;
use Illuminate\Support\Facades\Route;

Route::get('/test-query', [TestController::class, 'getQuery'])->name('getQuery');
Route::get('/sitemap.xml', SitemapController::class)->name('xml-map');
Route::get('/robots.txt', RobotsTxtController::class)->name('robots-txt');

Route::group(['prefix' => 'ajax'], function () {
    Route::controller(FormsController::class)->as('forms.')->prefix('/forms')->group(function () {
        Route::get('/{code}/get', 'getForm')->name('get');
        Route::post('/{code}/send', 'sendForm')->name('send');
    });

    Route::get('/get-categories', [MainPageController::class, 'getCategories']);
    Route::post('/get-subcategory', [MainPageController::class, 'getSubCategories']);
    Route::post('/get-products', [MainPageController::class, 'getProducts']);
    Route::post('/get-product', [MainPageController::class, 'getProduct']);

    Route::post('/get-form-config/{code}', [FormBuilderController::class, 'getFormConfig']);
    Route::post('/send-modal', [OrchidController::class, 'validateForm'])->name('validate.modals');
});

Route::group(['as' => 'web.'], function () {
    Route::controller(MainPageController::class)->prefix('/')->group(function () {
        Route::get('/', 'index')->name('page')->where('params', '.*');
    });

    Route::controller(PagesController::class)->middleware(Pages::class)->as('pages.')->prefix('/')->group(function () {
        Route::get('/{params?}', 'getPage')->name('page')->where('params', '.*');
    });
});


