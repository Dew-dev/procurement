<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BgNumberController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractPaymentTermController;
use App\Http\Controllers\MakerPaymentTermController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SuretyBondController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('contracts.index'))->name('home');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- Admin-only write routes (registered FIRST to avoid slug collisions) ---
    Route::middleware('role:admin')->group(function () {
        // Contract CRUD
        Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::put('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

        // Child upsert (create + update + delete in one submit per section)
        Route::put('contracts/{contract}/rfqs', [RfqController::class, 'upsert'])->name('contracts.rfqs.upsert');
        Route::put('contracts/{contract}/quotations', [QuotationController::class, 'upsert'])->name('contracts.quotations.upsert');
        Route::put('contracts/{contract}/purchase-orders', [PurchaseOrderController::class, 'upsert'])->name('contracts.purchase-orders.upsert');
        Route::put('contracts/{contract}/contract-payment-terms', [ContractPaymentTermController::class, 'upsert'])->name('contracts.contract-payment-terms.upsert');
        Route::put('contracts/{contract}/purchase-orders/{purchaseOrder}/maker-payment-terms', [MakerPaymentTermController::class, 'upsert'])->name('contracts.purchase-orders.maker-payment-terms.upsert');
        Route::put('contracts/{contract}/bg-numbers', [BgNumberController::class, 'upsert'])->name('contracts.bg-numbers.upsert');
        Route::put('contracts/{contract}/surety-bonds', [SuretyBondController::class, 'upsert'])->name('contracts.surety-bonds.upsert');
    });

    // --- Read-only routes (all authenticated users) ---
    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
});
