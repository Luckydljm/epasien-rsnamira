<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserKhanza extends Model
{
    /**
     * Kompatibel dengan tabel 'user' di SIMRS Khanza
     * id_user dan password dienkripsi AES_ENCRYPT
     */
    protected $table = 'user';
    public $timestamps = false;

    protected $hidden = ['password'];

    /**
     * Verifikasi login user biasa
     * Kompatibel dengan query SIMRS Khanza:
     * SELECT * FROM user WHERE id_user=AES_ENCRYPT(?,'nur') AND password=AES_ENCRYPT(?,'windi')
     */
    public static function verifyLogin(string $idUser, string $password): ?object
    {
        $keyUser = env('AES_KEY_USER', 'nur');
        $keyPass = env('AES_KEY_PASS', 'windi');

        $result = DB::selectOne(
            "SELECT *,
                AES_DECRYPT(id_user, ?) as kode_user
             FROM `user`
             WHERE id_user = AES_ENCRYPT(?, ?)
               AND password = AES_ENCRYPT(?, ?)
             LIMIT 1",
            [$keyUser, $idUser, $keyUser, $password, $keyPass]
        );

        return $result ?: null;
    }

    /**
     * Ambil nama pegawai dari tabel pegawai berdasarkan NIK
     */
    public static function getNamaPegawai(string $nik): string
    {
        $result = DB::selectOne(
            "SELECT nama FROM pegawai WHERE nik = ? LIMIT 1",
            [$nik]
        );
        return $result?->nama ?? $nik;
    }
}
