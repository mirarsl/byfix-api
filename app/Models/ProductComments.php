<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComments extends Model
{
    protected $table = "urun_yorum";

    protected $fillable = ['urun_id','baslik','isim','soyisim','yildiz','yorum','gizli','tarih','onay','uye_id'];

    public $timestamps = false;
}
