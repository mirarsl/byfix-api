<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Informations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InformationsController extends Controller
{
    public function index(Request $request)
    {
        if(Cache::has('info')){
            $value = Cache::get('info');
            return $value;
        }

        $information = Informations::select('site_baslik AS baslik','site_slogan AS slogan','site_desc AS aciklama','site_tel AS tel','site_whatsapp AS whatsapp','site_mail AS email','site_gsm AS gsm','adres_bilgisi AS adres')->get();
        $response = response()->json([
            'data' => $information[0],
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('info',$response, $seconds = 300);
        return $response;
    }
    
}
