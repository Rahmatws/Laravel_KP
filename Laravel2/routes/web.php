<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockController;

Route::get('/', [AuthController::class, 'loginView']);


Route::prefix('kp')->name('kp.')->group(function () {
    // Routes yang memerlukan onboarding check (untuk Admin)
    Route::middleware('onboarding.check')->group(function () {
        Route::get('dashboard', [StockController::class, 'dashboard'])->name('dashboard');
        Route::get('daftar_stok', [StockController::class, 'daftarStok'])->name('daftar_stok');
        Route::get('kelola_notifikasi', [StockController::class, 'kelolaNotifikasi'])->name('kelola_notifikasi');
        Route::post('notifikasi/toggle', [StockController::class, 'toggleNotif'])->name('notif.toggle');
        Route::post('notifikasi/save', [StockController::class, 'saveNotif'])->name('notif.save');
        Route::get('riwayat_stok', [StockController::class, 'riwayatStok'])->name('riwayat_stok');
        Route::get('koreksi_stok', [StockController::class, 'koreksiStok'])->name('koreksi_stok');
        Route::post('koreksi_stok', [StockController::class, 'koreksiStokPost'])->name('koreksi_stok.post');
        Route::post('item/update', [StockController::class, 'updateItem'])->name('item.update');
        Route::get('kategori_barang', [StockController::class, 'kategoriBarang'])->name('kategori_barang');
        Route::post('kategori_barang', [StockController::class, 'storeCategory'])->name('kategori_barang.store');
        Route::put('kategori_barang/{category}', [StockController::class, 'updateCategory'])->name('kategori_barang.update');
        Route::delete('kategori_barang/{category}', [StockController::class, 'destroyCategory'])->name('kategori_barang.destroy');
        Route::view('export_laporan', 'kp.export_laporan')->name('export_laporan');
        Route::get('export/process', [StockController::class, 'processExport'])->name('export.process');
        Route::get('import_penjualan', [StockController::class, 'importPenjualanView'])->name('import_penjualan');
        Route::post('import_penjualan', [StockController::class, 'importPenjualanPost'])->name('import_penjualan.post');
    });

    // Routes yang tidak memerlukan onboarding check
    Route::view('import', 'kp.import')->name('import');
    Route::post('import', [StockController::class, 'importSid'])->name('import.post');
    Route::get('analisis-stok', [StockController::class, 'grafikStok'])->name('analisis_stok');
    Route::post('import/preview', [StockController::class, 'previewSid'])->name('import.preview');
    Route::post('import/skip', [StockController::class, 'skipImport'])->name('import.skip');
    Route::get('detail_barang', [StockController::class, 'detailBarang'])->name('detail_barang');
    Route::get('detail_barang/edit/{id}', [StockController::class, 'editDetailBarangView'])->name('edit_detail_barang');
    Route::post('detail_barang/complete', [StockController::class, 'completeOnboarding'])->name('detail_barang.complete');
    Route::post('daftar-stok/complete', [StockController::class, 'completeDaftarStok'])->name('daftar_stok.complete');
    Route::get('registrasi', [AuthController::class, 'registerView'])->name('registrasi');
    Route::post('registrasi', [AuthController::class, 'register'])->name('register.post');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/debug-items', function () {
    $q = 'INJECTION';
    $items = \App\Models\Item::where('name', 'like', "%$q%")
        ->orWhere('code', 'like', "%$q%")
        ->get();
    return $items;
});
