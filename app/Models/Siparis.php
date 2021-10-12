<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siparis extends Model
{
    protected $table = "siparis";

    protected $fillable = ["ara_tutar",'kdv_tutar','kargo_tutar','toplam_tutar','isim','tel','eposta','adres','adres_fatura','sehir','postakodu','notlar','odeme_tip','siparis_id','siparis_tarih','siparis_no','siparis_durum','user_id'];

    public $timestamps = false;
}
