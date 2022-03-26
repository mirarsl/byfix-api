<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pages;

use Illuminate\Support\Facades\Cache;


class PagesController extends Controller
{
    public function index(Request $request)
    {
        if(Cache::has('pages')){
            $value = Cache::get('pages');
            return $value;
        }
        $response = response()->json([
            'data' => Pages::where('durum',1)->where('dil','tr')->get(),
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('pages',$response, $seconds = 300);
        return $response;
    }

    public function show(Request $request, $id)
    {
        if(Cache::has('page/'.$id.'')){
            $value = Cache::get('page/'.$id.'');
            return $value;
        }
        $article = Pages::where('id',$id)->where('dil','tr')->get();
        if (is_object($article)) {
            $response = response()->json([
                'data' => $article[0],
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('page/'.$id.'',$response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
}
