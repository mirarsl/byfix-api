<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestekMesaj extends Model
{
    protected $table = "uyeler_destek_mesaj";

    protected $fillable = ['id','mesaj','tarih','support_id','tip'];

    public $timestamps = false;
}
