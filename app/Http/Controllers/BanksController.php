<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banks;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BanksController extends Controller
{
    public function index(Request $request)
    {
        if(Cache::has('banks')){
            $value = Cache::get('banks');
            return $value;
        }
        $response = response()->json([
            'data' => Banks::where('durum',1)->get(),
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('banks',$response,$seconds = 300);
        return $response;
    }
}
