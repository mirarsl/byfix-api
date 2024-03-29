<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Products;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $offset = 0;
        $limit = 10;
        $include = "null";

        if($request->has('offset')){
            $offset = $request->get('offset');
        }
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if($request->has('include')){
            $include = $request->get('include');
        }

        if(Cache::has('kats/'.$offset.'/'.$limit.'/'.$include)){
            $value = Cache::get('kats/'.$offset.'/'.$limit.'/'.$include);
            return $value;
        }

        $data = Categories::where('durum',1)->where('dil','tr')->orderBy('sira','asc')->offset($offset)->limit($limit)->get();
        if($request->has('include')){
            foreach ($data as $key => $value) {
                $products = Products::where('durum',1)->where('dil','tr')->where('kat_id',$value['id'])->orderBy('tarih','desc')->get();
                $data[$key]['products'] = $products;
            }
        }
        $response = response()->json([
            'data' => $data,
            'status'=>200,
            'start' => $offset,
            'show' => $limit,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);

        Cache::put('kats/'.$offset.'/'.$limit.'/'.$include,$response, $seconds = 120);
        return $response;
    }

    public function show(Request $request, $id)
    {
        $include = "null";
        $offset = "null";
        $limit = "null";
        
        if($request->has('include')){
            $include = $request->get('include');
            if($include == "products"){
                $offset = 0;
                $limit = 10;

                if($request->has('offset')){
                    $offset = $request->get('offset');
                }
                if($request->has('limit')){
                    $limit = $request->get('limit');
                }
            }
        }

        if(Cache::has('kat/'.$id.'/'.$include.'/'.$offset.'/'.$limit)){
            $value = Cache::get('kat/'.$id.'/'.$include.'/'.$offset.'/'.$limit);
            return $value;
        }

        $article = Categories::where('id',$id)->where('dil','tr')->get();
        if (count($article) > 0) {
            if($include == "products"){
                $products = Products::where('kat_id',$id)->where('dil','tr')->offset($offset)->limit($limit)->get();
                $article['include'] = array('products' => $products,'start' => $offset, 'show' => $limit);
            }

            $response = response()->json([
                'data' => $article[0],
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('kat/'.$id.'/'.$include.'/'.$offset.'/'.$limit, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function products(Request $request, $id)
    {
        $offset = 0;
        $limit = 10;

        if($request->has('offset')){
            $offset = $request->get('offset');
        }
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if(Cache::has('katp/'.$id.'/'.$offset.'/'.$limit)){
            $value = Cache::get('katp/'.$id.'/'.$offset.'/'.$limit);
            return $value;
        }

        $article = Products::where('durum',1)->where('dil','tr')->where('kat_id',$id)->offset($offset)->limit($limit)->get();

        if (is_object($article) && count($article) > 0) {
            $response = response()->json([
                'data' => $article,
                'status'=>200,
                'start' => $offset,
                'show' => $limit,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('katp/'.$id.'/'.$offset.'/'.$limit, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
}
