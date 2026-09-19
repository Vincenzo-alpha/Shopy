<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Customer\InterestController;
use App\Http\Controllers\Customer\CustomerDealController;
use App\Http\Controllers\Seller\ProductServiceController;
use App\Http\Controllers\Seller\SellerDealController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public Marketplace Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/listing/{id}', [MarketplaceController::class, 'show'])->name('marketplace.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Customer Auth
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// Seller Auth
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/login', [SellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SellerAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [SellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerAuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('logout');
});

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Customer Protected Routes
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->name('customer.')->middleware('auth.customer')->group(function () {
    Route::get('/dashboard', [CustomerDealController::class, 'index'])->name('dashboard');
    
    // Interests
    Route::post('/interests/express', [InterestController::class, 'expressInterest'])->name('interests.express');
    Route::get('/interests', [InterestController::class, 'myInterests'])->name('interests');
    Route::post('/interests/{id}/withdraw', [InterestController::class, 'withdrawInterest'])->name('interests.withdraw');

    // Deals & Negotiation
    Route::get('/deals', [CustomerDealController::class, 'index'])->name('deals.index');
    Route::get('/deals/{id}', [CustomerDealController::class, 'show'])->name('deals.show');
    Route::post('/deals/{id}/counter-offer', [CustomerDealController::class, 'counterOffer'])->name('deals.counter');
    Route::post('/deals/{id}/accept-offer', [CustomerDealController::class, 'acceptOffer'])->name('deals.accept');

    // Sandbox Payment
    Route::get('/deals/{id}/payment', [CustomerDealController::class, 'paymentScreen'])->name('deals.payment');
    Route::post('/deals/{id}/payment', [CustomerDealController::class, 'processPayment'])->name('deals.payment.process');

    // Completed Deals History
    Route::get('/deals-completed', [CustomerDealController::class, 'completedDeals'])->name('deals.completed');
});

/*
|--------------------------------------------------------------------------
| Seller Protected Routes
|--------------------------------------------------------------------------
*/
Route::prefix('seller')->name('seller.')->middleware('auth.seller')->group(function () {
    Route::get('/dashboard', [SellerDealController::class, 'deals'])->name('dashboard');

    // Product & Service Management
    Route::get('/products', [ProductServiceController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductServiceController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductServiceController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductServiceController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductServiceController::class, 'update'])->name('products.update');
    Route::post('/products/{id}/toggle', [ProductServiceController::class, 'toggleAvailability'])->name('products.toggle');

    // Service Coverage Cities
    Route::get('/cities', [ProductServiceController::class, 'cities'])->name('cities.index');
    Route::post('/cities', [ProductServiceController::class, 'addCity'])->name('cities.add');
    Route::delete('/cities/{id}', [ProductServiceController::class, 'deleteCity'])->name('cities.delete');

    // Interests & Deals
    Route::get('/interests', [SellerDealController::class, 'interests'])->name('interests.index');
    Route::post('/interests/{id}/deal', [SellerDealController::class, 'initiateDeal'])->name('deals.initiate');
    
    Route::get('/deals', [SellerDealController::class, 'deals'])->name('deals.index');
    Route::get('/deals/{id}', [SellerDealController::class, 'showDeal'])->name('deals.show');
    Route::post('/deals/{id}/counter-offer', [SellerDealController::class, 'counterOffer'])->name('deals.counter');
    Route::post('/deals/{id}/accept-offer', [SellerDealController::class, 'acceptOffer'])->name('deals.accept');
    
    // Completion Key Verification
    Route::post('/deals/{id}/verify-completion', [SellerDealController::class, 'verifyCompletionKey'])->name('deals.verify');
    Route::get('/deals-completed', [SellerDealController::class, 'completedDeals'])->name('deals.completed');

    // Wallet & Earnings Ledger
    Route::get('/wallet', [SellerDealController::class, 'wallet'])->name('wallet.index');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth.admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Users Moderation
    Route::get('/sellers', [AdminDashboardController::class, 'sellers'])->name('sellers.index');
    Route::post('/sellers/{id}/status', [AdminDashboardController::class, 'updateSellerStatus'])->name('sellers.status');

    Route::get('/customers', [AdminDashboardController::class, 'customers'])->name('customers.index');
    Route::post('/customers/{id}/status', [AdminDashboardController::class, 'updateCustomerStatus'])->name('customers.status');

    // Marketplace Moderation
    Route::get('/listings', [AdminDashboardController::class, 'listings'])->name('listings.index');
    Route::get('/deals', [AdminDashboardController::class, 'deals'])->name('deals.index');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
});
