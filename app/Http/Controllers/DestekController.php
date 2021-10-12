<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destek;
use App\Models\DestekMesaj;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class DestekController extends Controller
{
    public function index(Request $request,$id)
    {
        if(Cache::has('desteks/'.$id)){
            $value = Cache::get('desteks/'.$id);
            return $value;
        }   

        $destek = Destek::where('user_id',$id)->get();
        if (count($destek) > 0) {
            $response = response()->json([
                'data' => $destek,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('desteks/'.$id,$response,$seconds=180);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
    public function show(Request $request,$id,$did)
    {
        if(Cache::has('destek/'.$id.'/'.$did)){
            $value = Cache::get('destek/'.$id.'/'.$did);
            return $value;
        }   

        $destek = Destek::where('user_id',$id)->where('id',$did)->get();
        if (count($destek) > 0) {

            $mesajlar = DestekMesaj::where('support_id',$destek[0]['support_id'])->get();

            $destek[0]['mesajlar'] = $mesajlar;
            $response = response()->json([
                'data' => $destek,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('destek/'.$id.'/'.$did,$response,$seconds=180);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
    public function create(Request $request,$id)
    {
        if(Cache::has('destekc/'.$id)){
            if(Cache::get('destekc/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık destek talebi oluşturdunuz.'],404);
            }else{
                Cache::increment('destekc/'.$id,1, $seconds = 60);
            }
        }else Cache::put('destekc/'.$id, 1 , $seconds = 60);

        $req = $request->all();
        $req['user_id'] = $id;
        $req['support_id'] = rand(1111111111,9999999999);
        $req['durum'] = 1;
        $req['tarih'] = date('Y-m-d h:i:s');
        $validator = Validator::make(
            $req,
            [
                'user_id' => 'required|numeric',
                'support_id' => 'required|numeric',
                'durum' => 'required|numeric',
                'tarih' => 'required|date',
                'konu' => 'required',
                'mesaj' => 'required'
            ],
            [
                'user_id.required' => 'User ID alanı mecburidir.',
                'user_id.numeric' => 'Lütfen geçerli bir user_id alanı giriniz.',
                'support_id.required' => 'Support ID alanı mecburidir.',
                'support_id.numeric' => 'Lütfen geçerli bir support ID giriniz.',
                'durum.required' => 'Durum alanı mecburidir.',
                'durum.numeric' => 'Lütfen geçerli bir durum giriniz.',
                'tarih.required' => 'Tarih alanı mecburidir.',
                'tarih.date' => 'Lütfen geçerli bir tarih giriniz.',
                'konu.required' => 'Konu alanı mecburidir.',
                'mesaj.required' => 'Mesaj alanı mecburidir.',
            ]
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $mesaj = $req['mesaj'];
            $destek = Destek::create($req);
            $mes = DestekMesaj::create(['mesaj' => $mesaj,'tarih'=>$req['tarih'],'support_id'=>$req['support_id'],'tip' => 1]);
            $destek['mesaj'] = $mes;
            return response()->json(['data' => $destek,'status'=> 200],200);

        }
    }

    public function add(Request $request, $id, $did)
    {
        if(Cache::has('destekm/'.$id)){
            if(Cache::get('destekm/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık destek talebi oluşturdunuz.'],404);
            }else{
                Cache::increment('destekm/'.$id,1, $seconds = 60);
            }
        }else Cache::put('destekm/'.$id, 1 , $seconds = 60);

        $req = $request->all();
        
        $req['user_id'] = $id;
        $req['tip'] = 1;
        $req['tarih'] = date('Y-m-d h:i:s');
        
        $validator = Validator::make(
            $req,
            [
                'user_id' => 'required|numeric',
                'support_id' => 'numeric',
                'tip' => 'required|numeric',
                'tarih' => 'required|date',
                'mesaj' => 'required'
            ],
            [
                'user_id.required' => 'User ID alanı mecburidir.',
                'user_id.numeric' => 'Lütfen geçerli bir user_id alanı giriniz.',
                'support_id.numeric' => 'Lütfen geçerli bir support ID giriniz.',
                'tip.required' => 'Durum alanı mecburidir.',
                'tip.numeric' => 'Lütfen geçerli bir tip giriniz.',
                'tarih.required' => 'Tarih alanı mecburidir.',
                'tarih.date' => 'Lütfen geçerli bir tarih giriniz.',
                'mesaj.required' => 'Mesaj alanı mecburidir.',
            ]
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $destek = Destek::where('user_id',$id)->where('id',$did)->get();
            if(count($destek) > 0){
                $req['support_id'] = $destek[0]['support_id'];
                $mesaj = DestekMesaj::create($req);
                return $mesaj;
            }else{
                return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
            }

        }
    }
}
