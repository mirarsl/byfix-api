<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductPictures;
use App\Models\ProductComments;
use App\Models\ProductVariant;
use App\Models\ProductVariantOptions;
use App\Models\Categories;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $offset = 0;
        $limit = 10;

        if($request->has('offset')){
            $offset = $request->get('offset');
        }
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if(Cache::has('pros/'.$limit.'/'.$offset)){
            $value = Cache::get('pros/'.$limit.'/'.$offset);
            return $value;
        }
        $data = Products::where('durum',1)->orderBy('tarih','desc')->offset($offset)->limit($limit)->get();
        $response = response()->json([
            'data' => $data,
            'status'=>200,
            'start' => $offset,
            'show' => $limit,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('pros/'.$limit.'/'.$offset, $response, $seconds = 300);
        return $response;
    }

    public function show(Request $request, $id)
    {
        $include = "null";
        $offset = "null";
        $limit = "null";
        $variant = "null";

        if($request->has('include')){
            $include = $request->get('include');
            $includes = explode('|', $include);
            $inc = array();

            foreach ($includes as $key => $value) {
                if($value == "pictures"){
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
        }

        if($request->has("variant")){
            $variant = $request->get("variant");
        }

        if(Cache::has('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant)){
            $value = Cache::get('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant);
            return $value;
        }

        $article = Products::where('id',$id)->get();
        if (is_object($article)) {

            if($request->has('include')){
                foreach ($includes as $key => $value) {
                    if($value == "pictures"){
                        $products = ProductPictures::where('urun_id',$id)->offset($offset)->limit($limit)->get();

                        $inc = array_merge($inc, array('pictures' => $products,'start' => $offset, 'show' => $limit));

                    }else if($value == "comments"){
                        $comments = ProductComments::where('urun_id',$id)->get();
                        $inc = array_merge($inc, array('comments' => $comments));
                    }
                }
                $article["includes"] = $inc;
            }

            if($request->has("variant")){
                if($variant == 'true'){
                    $variants = ProductVariant::where('urun_id',$id)->get();
                    foreach ($variants as $key => $value) {
                        $options = ProductVariantOptions::where('varyant_id',$variants[0]['id'])->get();
                        $variants[$key]["options"] = $options;
                        $article["variants"] = $variants;
                    }
                }
            }


            $response = response()->json([
                'data' => $article[0],
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant,$response, $seconds = 300);
            return $response;

        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function discounts(Request $request)
    {
        $offset = 0;
        $limit = 10;

        if($request->has('offset')){
            $offset = $request->get('offset');
        }
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if(Cache::has('prodisc/'.$limit.'/'.$offset)){
            $value = Cache::get('prodisc/'.$limit.'/'.$offset);
            return $value;
        }

        $response = response()->json([
            'data' => Products::where('durum',1)->where('eski_fiyat',"!=",0)->offset($offset)->limit($limit)->get(),
            'status'=>200,
            'start' => $offset,
            'show' => $limit,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);
        Cache::put('prodisc/'.$limit.'/'.$offset, $response, $seconds = 300);
        return $response;
    }

    public function pictures(Request $request, $id)
    {

        $offset = 0;
        $limit = 10;

        if($request->has('offset')){
            $offset = $request->get('offset');
        }
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if(Cache::has('prop/'.$id.'/'.$limit.'/'.$offset)){
            $value = Cache::get('prop/'.$id.'/'.$limit.'/'.$offset);
            return $value;
        }

        $article = ProductPictures::where('urun_id',$id)->offset($offset)->limit($limit)->get();
        if (is_object($article) && count($article) > 0) {
            $response = response()->json([
                'data' => $article,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('prop/'.$id.'/'.$limit.'/'.$offset, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function comments(Request $request, $id)
    {

        if(Cache::has('proc/'.$id)){
            $value = Cache::get('proc/'.$id);
            return $value;
        }   
        $article = ProductComments::where('urun_id',$id)->get();
        if (is_object($article) && count($article) > 0) {
            $response = response()->json([
                'data' => $article,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('proc/'.$id, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
}
