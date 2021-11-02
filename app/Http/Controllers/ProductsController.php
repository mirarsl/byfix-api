<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductPictures;
use App\Models\ProductMatchs;
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
        $match = "null";

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

        if($request->has("match")){
            $match = $request->get("match");
        }

        if(Cache::has('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant.'/'.$match)){
            $value = Cache::get('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant.'/'.$match);
            return $value;
        }


        $article = Products::where('id',$id)->get();
        if (count($article) > 0) {

            if($request->has('include')){
                foreach ($includes as $key => $value) {
                    if($value == "pictures"){
                        $products = ProductPictures::where('urun_id',$id)->offset($offset)->limit($limit)->get();

                        $inc = array_merge($inc, array('pictures' => $products,'start' => $offset, 'show' => $limit));

                    }else if($value == "comments"){
                        $comments = ProductComments::where('urun_id',$id)->where('onay',1)->orderBy('beneficial','desc')->orderBy('id','asc')->get();
                        $inc = array_merge($inc, array('comments' => $comments));
                    }
                }
                $article[0]["includes"] = $inc;
            }

            if($request->has("variant")){
                if($variant == 'true'){
                    $variants = ProductVariant::where('urun_id',$id)->get();
                    foreach ($variants as $key => $value) {
                        $options = ProductVariantOptions::where('varyant_id',$variants[$key]['id'])->get();
                        $variants[$key]["options"] = $options;
                        $article[0]["variants"] = $variants;
                    }
                }
            }

            if($request->has("match")){
                if($match == 'true'){
                    $matchs = ProductMatchs::where('pid',$id)->get();
                    foreach ($matchs as $key => $value) {
                        $product = Products::where('id',$value['pids'])->get();
                        $matchs[$key]['product'] = $product;
                        $article[0]['matched_products'] = $matchs;
                    }
                }
            }


            $response = response()->json([
                'data' => $article[0],
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('pro/'.$id.'/'.$include.'/'.$limit.'/'.$offset.'/'.$variant.'/'.$match,$response, $seconds = 300);
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
        $article = ProductComments::where('urun_id',$id)->orderBy('beneficial','desc')->orderBy('id','asc')->get();
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

    public function comment(Request $request, $id, $cid)
    {

        if(Cache::has('proc/'.$id.'/'.$cid)){
            $value = Cache::get('proc/'.$id.'/'.$cid);
            return $value;
        }   
        $article = ProductComments::where('urun_id',$id)->where('id',$cid)->get();
        if (is_object($article) && count($article) > 0) {
            $response = response()->json([
                'data' => $article,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('proc/'.$id.'/'.$cid, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
    public function commentpoint(Request $request, $id, $cid)
    {
        if(Cache::has('procom/'.$id.'/'.$cid)){
            if(Cache::get('procom/'.$id.'/'.$cid) >= 1){
                return response()->json(['error'=> 'Çok sık bildirim oluşturmayı denediniz.'],404);
            }else{
                Cache::increment('procom/'.$id.'/'.$cid,1, $seconds = 30);
            }
        }else Cache::put('procom/'.$id.'/'.$cid, 1 , $seconds = 30);

        $req = $request->all();
        $validator = Validator::make(
            $req,
            [
                'type' => 'required'
            ],
            [
                'type.required' => 'Lütfen Tipi seçiniz',
            ]
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $article = ProductComments::where('urun_id',$id)->where('id',$cid)->get();
            if (is_object($article) && count($article) > 0) {
                if($req['type'] == 0){
                    $last =  ProductComments::where('urun_id',$id)->where('id',$cid)->increment('beneficial',1);
                }else{
                    $last =  ProductComments::where('urun_id',$id)->where('id',$cid)->increment('useless',1);
                }
                $article = ProductComments::where('urun_id',$id)->where('id',$cid)->get();
                $response = response()->json([
                    'data' => $article,
                    'status'=>200,
                    'created_at' => date('Y-m-d h:i:s',time())
                ],200);

                return $response;
            }else{
                return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
            }
        }
    }
}
