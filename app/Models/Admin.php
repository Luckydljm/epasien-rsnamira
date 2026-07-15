<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admin extends Model
{
    protected $table = 'admin';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['usere', 'passworde'];
    protected $hidden   = ['passworde'];

    /**
     * Verifikasi login Admin Utama
     * Kompatibel dengan query SIMRS Khanza:
     * SELECT * FROM admin WHERE usere=AES_ENCRYPT(?,'nur') AND passworde=AES_ENCRYPT(?,'windi')
     */
    public static function verifyLogin(string $username, string $password): bool
    {
        $keyUser = env('AES_KEY_USER', 'nur');
        $keyPass = env('AES_KEY_PASS', 'windi');

        $count = DB::selectOne(
            "SELECT COUNT(*) as jml FROM admin
             WHERE usere = AES_ENCRYPT(?, ?) AND passworde = AES_ENCRYPT(?, ?)",
            [$username, $keyUser, $password, $keyPass]
        );

        return $count && $count->jml >= 1;
    }
}
