<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // Set locale Indonesia untuk Carbon
        Carbon::setLocale('id');
        
        // Atau buat custom helper
        if (!function_exists('formatTanggalIndonesia')) {
            function formatTanggalIndonesia($date) {
                return Carbon::parse($date)->translatedFormat('j F Y');
            }
        }
    }
}
