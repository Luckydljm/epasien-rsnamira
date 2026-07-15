<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel tracker — mencatat aktivitas login/logout
     * Mirip dengan fungsi tracker di SIMRS Khanza
     */
    public function up(): void
    {
        if (! Schema::hasTable('tracker')) {
            Schema::create('tracker', function (Blueprint $table) {
                $table->id();
                $table->string('id_user', 100)->nullable();
                $table->string('nama', 150)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->enum('aksi', ['Login', 'Logout'])->default('Login');
                $table->date('tgl_aksi')->nullable();
                $table->time('jam_aksi')->nullable();
                $table->timestamps();

                $table->index('id_user');
                $table->index('tgl_aksi');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tracker');
    }
};
