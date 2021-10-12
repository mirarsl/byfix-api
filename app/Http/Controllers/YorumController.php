<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductComments;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class YorumController extends Controller
{
    public function index($id)
    {
        if(Cache::has('yorums/'.$id)){
            $value = Cache::get('yorums/'.$id);
            return $value;
        }   

        $yorum = ProductComments::where('uye_id',$id)->get();
        if (count($yorum) > 0) {
            $response = response()->json([
                'data' => $yorum,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('yorums/'.$id,$response,$seconds=180);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function show($id,$yid)
    {
        if(Cache::has('yorum/'.$id.'/'.$yid)){
            $value = Cache::get('yorum/'.$id.'/'.$yid);
            return $value;
        }   

        $yorum = ProductComments::where('uye_id',$id)->where('id',$yid)->get();
        if (count($yorum) > 0) {
            $response = response()->json([
                'data' => $yorum,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('yorum/'.$id.'/'.$yid, $response, $seconds=180);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function add(Request $request,$id,$pid)
    {

        if(Cache::has('commentc/'.$id)){
            if(Cache::get('commentc/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık yorum yapmayı denediniz.'],404);
            }else{
                Cache::increment('commentc/'.$id,1,$seconds = 60);
            }
        }else Cache::put('commentc/'.$id, 1 , $seconds = 60);

        $req = $request->all();
        $req['urun_id'] = $pid;
        $req['uye_id'] = $id;
        $req['onay'] = 0;
        $req["tarih"] = date('Y-m-d h:i:s',time());
        $validator = Validator::make(
            $req,
            [
                'baslik' => 'required',
                'isim' => 'required',
                'soyisim' => 'required',
                'yildiz' => 'required|numeric',
                'yorum' => 'required',
                'gizli' => 'required|numeric',
                'tarih' => 'required|date',
                'onay' => 'required|numeric',
                'uye_id' => 'required',
            ],
            [
                'baslik.required' => 'Başlık alanı mecburidir.',
                'isim.required' => 'İsim alanı mecburidir.',
                'soyisim.required' => 'Soyisim alanı mecburidir.',
                'yildiz.required' => 'Yıldız alanı mecburidir.',
                'yildiz.numeric' => 'Lütfen geçerli bir sayı giriniz.',
                'yorum.required' => 'Yorum alanı mecburidir.',
                'gizli.required' => 'Gizli alanı mecburidir.',
                'gizli.numeric' => 'Lütfen geçerli bir sayı giriniz.',
                'tarih.required' => 'Tarih alanı mecburidir.',
                'tarih.date' => 'Lütfen geçerli bir tarih giriniz.',
                'onay.required' => 'Onay alanı mecburidir.',
                'onay.numeric' => 'Lütfen geçerli bir sayı giriniz.',
                'uye_id.required' => 'Uye ID alanı mecburidir.', 
            ]
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>300],300);
        }else{
            $urun = Products::where('id',$pid)->get();
            if(count($urun) > 0){
                $comment = ProductComments::create($req);
                return $comment;
            }else{
                return response()->json(["message"=>"Ürün Bulunamadı",'status'=>404],404);
            }
        }
    }
}
