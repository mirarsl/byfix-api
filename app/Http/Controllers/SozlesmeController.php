<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sozlesme;

use Illuminate\Support\Facades\Cache;

class SozlesmeController extends Controller
{
    public function index(Request $request)
    {
        if(Cache::has('sozlesme')){
            $value = Cache::get('sozlesme');
            return $value;
        }

        $response =  response()->json([
            'data' => Sozlesme::get(),
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('sozlesme',$response, $seconds = 600);
        return $response;
    }
}
