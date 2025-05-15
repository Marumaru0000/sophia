<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * このモデルで使うガード名
     */
    protected $guard = 'admin';

    /**
     * マスアサイン可能な属性
     */
    protected $fillable = [
        'email',
    ];

    /**
     * パスワードは .env の固定値を使うため、DBに保存しない
     * もしパスワードカラムを使う場合は $fillable に 'password' を追加してください。
     */

    /**
     * タイムスタンプ不要なら false に
     */
    public $timestamps = false;

    /**
     * シークレット情報 を隠す
     */
    protected $hidden = [
        // 'password',
    ];
}