<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiparisUrunler extends Model
{
    protected $table = "siparis_urunler";

    protected $fillable = ['urun_id','siparis_id','urun_baslik','adet','tutar','kdv_tutar','kargo_tutar','varyantlar'];

    public $timestamps = false;

}
