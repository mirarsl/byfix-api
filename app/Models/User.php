<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "uyeler";

    protected $fillable = ["isim","soyisim","telefon","eposta","tcno","cinsiyet","uyesifre","ip","tarih"];

    public $timestamps = false;
}
