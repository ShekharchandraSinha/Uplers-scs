<?php

use App\Http\Controllers\BackendController\EspController;
use App\Http\Controllers\BackendController\FrameworkController;
use App\Http\Controllers\BackendController\PmsController;
use App\Http\Controllers\BackendController\PortfolioController;
use App\Http\Controllers\BackendController\UserController;
use App\Http\Controllers\FrontendController\PortfolioController as FrontendControllerPortfolioController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('index'); // handles fallback

// Admin protected routes
Route::name('admin.')->prefix('admin')->middleware('auth')->group(function () {
    Route::name('user.')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/search-sort-paginate', [UserController::class, 'indexSearchPaginateSort'])->name('search-sort-paginate');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/create', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [UserController::class, 'update'])->name('update');
    });

    Route::name('esp.')->prefix('esps')->group(function () {
        Route::get('/', [EspController::class, 'index'])->name('index');
        Route::post('/search-sort-paginate', [EspController::class, 'indexSearchPaginateSort'])->name('search-sort-paginate');
        Route::get('/create', [EspController::class, 'create'])->name('create');
        Route::post('/create', [EspController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [EspController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [EspController::class, 'update'])->name('update');
    });

    Route::name('pms.')->prefix('pms')->group(function () {
        Route::get('/', [PmsController::class, 'index'])->name('index');
        Route::post('/search-sort-paginate', [PmsController::class, 'indexSearchPaginateSort'])->name('search-sort-paginate');
        Route::get('/create', [PmsController::class, 'create'])->name('create');
        Route::post('/create', [PmsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PmsController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [PmsController::class, 'update'])->name('update');
    });

    Route::name('framework.')->prefix('frameworks')->group(function () {
        Route::get('/', [FrameworkController::class, 'index'])->name('index');
        Route::post('/search-sort-paginate', [FrameworkController::class, 'indexSearchPaginateSort'])->name('search-sort-paginate');
        Route::get('/create', [FrameworkController::class, 'create'])->name('create');
        Route::post('/create', [FrameworkController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [FrameworkController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [FrameworkController::class, 'update'])->name('update');
    });

    Route::name('portfolio.')->prefix('portfolios')->group(function () {
        Route::get('/', [PortfolioController::class, 'index'])->name('index');
        Route::post('/search-sort-paginate', [PortfolioController::class, 'indexSearchPaginateSort'])->name('search-sort-paginate');
        Route::get('/create', [PortfolioController::class, 'create'])->name('create');
        Route::post('/create', [PortfolioController::class, 'store'])->name('store');

        Route::get('/edit/{id}', [PortfolioController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}/slugCheck', [PortfolioController::class, 'editSlugCheck'])->name('edit.slug-check');

        Route::patch('/update/{portfolioId}', [PortfolioController::class, 'update'])->name('update');

        // For preview
        Route::get('/portfolio-preview/{previewId}/{highlightSection?}', [PortfolioController::class, 'portfolioPreview'])->name('portfolio-preview.show');
        Route::post('/portfolio-preview/{previewId}', [PortfolioController::class, 'portfolioPreviewStore'])->name('portfolio-preview.store');

        // Gallery uploader
        Route::post('/edit/gallery/upload/', [PortfolioController::class, 'galleryUpload'])->name('edit.gallery.upload');
        // Profile uploader
        Route::post('/edit/profile-photo/upload/', [PortfolioController::class, 'profilePhotoUpload'])->name('edit.profile-photo.upload');
    });
});

Route::get('portfolio/{portfolioSlug}/{templateId?}', [FrontendControllerPortfolioController::class, 'index'])->name('public.portfolio');
Route::get('portfolio-download/{portfolioSlug}/{pageHeight}', [FrontendControllerPortfolioController::class, 'downloadPdf'])->name('public.portfolio.download-pdf');
Route::post('portfolio/layout-update/{portfolioSlug}', [FrontendControllerPortfolioController::class, 'updateLayout'])->name('public.portfolio.layout-update')->middleware('auth');

// Auth routes
Auth::routes();

Route::get('/logout', function () {
    Auth::logout();
    return redirect()->route('index');
})->name('auth.logout');
