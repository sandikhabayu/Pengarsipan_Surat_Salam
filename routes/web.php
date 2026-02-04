<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\SuratTemplateController;

// Redirect berdasarkan role
Route::get('/dashboard', function () {
    return auth()->user()->role === 'petugas' 
        ? redirect()->route('petugas.dashboard')
        : redirect()->route('kepala-sekolah.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ---------------- PETUGAS ----------------
Route::prefix('petugas')->middleware(['auth', 'role:petugas'])->group(function () {
    // Dashboard Petugas
    Route::get('/dashboard', function () {
        $countSuratMasuk = \App\Models\SuratMasuk::count();
        $countSuratKeluar = \App\Models\SuratKeluar::count();
        $countArsip = \App\Models\Arsip::count();

        $latestSuratMasuks = \App\Models\SuratMasuk::latest()->take(5)->get();
        $latestSuratKeluars = \App\Models\SuratKeluar::latest()->take(5)->get();

        return view('petugas.dashboard', compact(
            'countSuratMasuk',
            'countSuratKeluar',
            'countArsip',
            'latestSuratMasuks',
            'latestSuratKeluars'
        ));
    })->name('petugas.dashboard');

    // Surat Masuk
    Route::resource('surat-masuk', SuratMasukController::class)->names([
        'index' => 'petugas.surat-masuk.index',
        'create' => 'petugas.surat-masuk.create',
        'store' => 'petugas.surat-masuk.store',
        'show' => 'petugas.surat-masuk.show',
        'edit' => 'petugas.surat-masuk.edit',
        'update' => 'petugas.surat-masuk.update',
        'destroy' => 'petugas.surat-masuk.destroy'
    ]);

    // Surat Keluar
    Route::resource('surat-keluar', SuratKeluarController::class)->names([
        'index' => 'petugas.surat-keluar.index',
        'create' => 'petugas.surat-keluar.create',
        'store' => 'petugas.surat-keluar.store',
        'show' => 'petugas.surat-keluar.show',
        'edit' => 'petugas.surat-keluar.edit',
        'update' => 'petugas.surat-keluar.update',
        'destroy' => 'petugas.surat-keluar.destroy'
    ]);

    // Arsip
    Route::prefix('arsip')->group(function () {
        Route::get('/', [ArsipController::class, 'index'])->name('petugas.arsip.index');
        Route::post('/arsipkan/{id}', [ArsipController::class, 'arsipkan'])->whereNumber('id')->name('petugas.arsip.arsipkan');
        Route::get('/arsip/{id}/file', [ArsipController::class, 'showFile'])
            ->name('arsip.showFile')
            ->middleware(['auth', 'role:petugas,kepala-sekolah']);
        Route::delete('/{id}', [ArsipController::class, 'destroy'])->name('petugas.arsip.destroy');
    });

    // Laporan Petugas
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('petugas.laporan.index');
        Route::post('/generate', [LaporanController::class, 'generate'])->name('petugas.laporan.generate');
        Route::get('/pdf', [LaporanController::class, 'exportPdf'])->name('petugas.laporan.pdf');
    });

    // Buat Surat Petugas
    Route::prefix('petugas/surat-template')->name('petugas.surat-template.')->group(function () {
        Route::get('/', [SuratTemplateController::class, 'index'])->name('index');
        Route::get('/create', [SuratTemplateController::class, 'create'])->name('create');
        Route::post('/', [SuratTemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratTemplateController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SuratTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SuratTemplateController::class, 'update'])->name('update'); // Method PUT
        Route::delete('/{id}', [SuratTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [SuratTemplateController::class, 'download'])->name('download');
    });
         // Tambahkan route khusus untuk PDF
    Route::get('surat-template/{id}/download', [SuratTemplateController::class, 'download'])
         ->name('petugas.surat-template.download');

    // Profile Petugas
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('petugas.profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('petugas.profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('petugas.profile.update');
    });
}); // This closes the petugas group

// ---------------- KEPALA SEKOLAH ----------------
Route::prefix('kepala-sekolah')->middleware(['auth', 'role:kepala_sekolah'])->group(function () {
    // Dashboard Kepala Sekolah
    Route::get('/dashboard', function () {
        $countSuratMasuk = \App\Models\SuratMasuk::count();
        $countSuratKeluar = \App\Models\SuratKeluar::count();
        $countArsip = \App\Models\Arsip::count();

        $latestSuratMasuks = \App\Models\SuratMasuk::latest()->take(5)->get();
        $latestSuratKeluars = \App\Models\SuratKeluar::latest()->take(5)->get();

        return view('kepala-sekolah.dashboard', compact(
            'countSuratMasuk',
            'countSuratKeluar',
            'countArsip',
            'latestSuratMasuks',
            'latestSuratKeluars'
        ));
    })->name('kepala-sekolah.dashboard');

    // Arsip
    Route::prefix('arsip')->group(function () {
        Route::get('/', [ArsipController::class, 'indexKepalaSekolah'])->name('kepala-sekolah.arsip.index');
    });

    // Laporan Kepala Sekolah
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'indexKepalaSekolah'])->name('kepala-sekolah.laporan.index');
    });

    // Profile Kepala Sekolah
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'indexKepalaSekolah'])->name('kepala-sekolah.profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('kepala-sekolah.profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('kepala-sekolah.profile.update');
    });
}); 

// ---------------- AUTH ----------------
require __DIR__.'/auth.php';

// ---------------- REDIRECT ROOT KE LOGIN ----------------
Route::get('/', function () {
    return redirect()->route('login');
});
