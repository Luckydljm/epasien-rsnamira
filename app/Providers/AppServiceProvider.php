<?php

namespace App\Providers;

use App\Services\TrackSqlService;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Sediakan data hitungan pasien untuk sidebar di semua halaman
        View::composer('layouts.app', SidebarComposer::class);

        // Track SQL otomatis ke tabel trackersql untuk operasi INSERT, UPDATE, DELETE persis seperti di SIMRS Khanza
        DB::listen(function ($query) {
            TrackSqlService::handleQueryEvent($query);
        });
    }
}
