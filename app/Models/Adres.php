<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adres extends Model
{
    protected $table = "uyeler_adres";

    protected $fillable = ['baslik','sehir','ilce','posta_kodu','adres','fatura_adres','uye_id','adres_id'];

    public $timestamps = false;
    
}
