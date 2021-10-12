<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destek extends Model
{
    protected $table = "uyeler_destek";

    protected $fillable = ['user_id','support_id','durum','tarih','konu'];

    public $timestamps = false;
}
