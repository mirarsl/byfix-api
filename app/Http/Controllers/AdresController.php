<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Adres;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


class AdresController extends Controller
{
    public function index(Request $request, $id)
    {
        if(Cache::has('adresler/'.$id)){
            $value = Cache::get('adresler/'.$id);
            return $value;
        }
        $adresler = Adres::where('uye_id',$id)->get();
        if (count($adresler) > 0) {
            $response = response()->json([
                'data' => $adresler,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('adresler/'.$id,$response, $seconds = 60);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function show(Request $request,$id,$aid)
    {
        if(Cache::has('adres/'.$id.'/'.$aid)){
            $value = Cache::get('adres/'.$id.'/'.$aid);
            return $value;
        }
        $adresler = Adres::where('uye_id',$id)->where('id',$aid)->get();
        if (count($adresler) > 0) {
            $response = response()->json([
                'data' => $adresler,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('adres/'.$id.'/'.$aid,$response, $seconds = 60);
            return $response; 
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }  
    }
    public function add(Request $request,$id)
    {
        if(Cache::has('adresc/'.$id)){
            if(Cache::get('adresc/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık adres kaydı oluşturmayı denediniz.'],404);
            }else{
                Cache::increment('adresc/'.$id,1, $seconds = 60);
            }
        }else Cache::put('adresc/'.$id, 1 , $seconds = 60);

        $req = $request->all(); 
        $req["uye_id"] = $id;
        $req["adres_id"] = rand(1111111111,9999999999);

        if(!isset($req['fatura_adres'])){
            $req['fatura_adres'] = $req['adres'];
        }

        $validator = Validator::make(
            $req,
            [
                'baslik' => 'required',
                'sehir' => 'required',
                'ilce' => 'required',
                'posta_kodu' => 'required|numeric',
                'adres' => 'required',
                'fatura_adres'=> 'nullable',
                'uye_id' => 'required',
                'adres_id' => 'unique:uyeler_adres|required',
            ],
            [
                'baslik.required' => "Başlık alanı mecburidir.",
                'sehir.required' => 'Şehir alanı mecburidir.',
                'ilce.required' => 'İlçe alanı mecburidir.',
                'posta_kodu.required' => 'Posta kodu alanı mecburidir.',
                'posta_kodu.numeric' => "Lütfen geçerli bir posta kodu giriniz.",
                'adres.required' => "Adres alanı mecburidir.",
                'uye_id.required' => "Üye ID alanı mecburidir.",
                'adres_id.required' => "Adres ID alanı mecburidir.",
                'adres_id.unique' => "Bu adres ID ile daha önce kayıt oluşturulmuştur.",
            ],
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $adres = Adres::create($req);
            return response()->json(['data' => $adres,'status'=> 200],200);

        }
    }
    public function delete(Request $request, $id,$aid)
    {
        if(Cache::has('adresd/'.$id)){
            if(Cache::get('adresd/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık adres silmeyi denediniz.'],404);
            }else{
                Cache::increment('adresd/'.$id,1, $seconds = 60);
            }
        }else Cache::put('adresd/'.$id, 1 , $seconds = 60);

        $req = $request->all();
        $req['uye_id'] = $id;
        $req['id'] = $aid;

        $validator = Validator::make(
            $req,
            [
                'uye_id' => 'required',
                'id' => 'required',
            ],
            [
                'uye_id.required' => 'Üye ID alanı mecburidir.',
                'id.required' => 'Adres ID alanı mecburidir.',
            ]
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $adres = Adres::where('id',$aid)->where('uye_id',$id)->get();
            if(count($adres) > 0){
                Adres::where('id',$aid)->where('uye_id',$id)->delete();
                return response()->json(["message"=>"Adres başarılı bir şekilde kaldırıldı.",'status'=>200],200);
            }else{
                return response()->json(["message"=>"Kayıtlı Adres Bulunamadı",'status'=>404],404);
            }
        }
    }
    public function update(Request $request, $id,$aid)
    {
        if(Cache::has('adresu/'.$id)){
            if(Cache::get('adresu/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık adres güncellemeyi denediniz.'],404);
            }else{
                Cache::increment('adresu/'.$id,1, $seconds = 60);
            }
        }else Cache::put('adresu/'.$id, 1 , $seconds = 60);

        $req = $request->all();
        if(!isset($req['fatura_adres'])){
            $req['fatura_adres'] = $req['adres'];
        }
        $validator = Validator::make(
            $req,
            [
                'baslik' => 'required',
                'sehir' => 'required',
                'ilce' => 'required',
                'posta_kodu' => 'required|numeric',
                'adres' => 'required',
                'fatura_adres'=> 'nullable',
            ],
            [
                'baslik.required' => "Başlık alanı mecburidir.",
                'sehir.required' => 'Şehir alanı mecburidir.',
                'ilce.required' => 'İlçe alanı mecburidir.',
                'posta_kodu.required' => 'Posta kodu alanı mecburidir.',
                'posta_kodu.numeric' => "Lütfen geçerli bir posta kodu giriniz.",
                'adres.required' => "Adres alanı mecburidir.",
            ],
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $adres = Adres::where('id',$aid)->get();
            if(count($adres) > 0){
                Adres::where('id',$aid)->where('uye_id',$id)->update($req);
                $new = Adres::where('id',$aid)->where('uye_id',$id)->get();
                return response()->json(['data'=>$new, "message"=>"Adres başarılı bir şekilde güncellendi.",'status'=>200],200);
            }else{
                return response()->json(["message"=>"Kayıtlı Adres Bulunamadı",'status'=>404],404);
            }
        }
    }
}
