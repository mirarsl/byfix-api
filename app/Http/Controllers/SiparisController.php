<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siparis;
use App\Models\SiparisUrunler;
use App\Models\Products;

use App\Http\Controllers\MailController;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class SiparisController extends Controller
{

    public function siparisler(Request $request, $id)
    {
        $include = "null";

        $includes = array();
        if($request->has('include')){
            $include = $request->get('include');
            $includes = explode('|', $include);
        }

        if(Cache::has('siparis/'.$id.'/'.$include)){
            $value = Cache::get('siparis/'.$id.'/'.$include);
            return $value;
        }

        $siparis = Siparis::where('user_id',$id)->get();
        if(is_object($siparis) && count($siparis)> 0){
            if(in_array('urunler',$includes) || in_array('count',$includes)){
                foreach ($siparis as $key => $value) {
                    $urunler = SiparisUrunler::where('siparis_id',$siparis[$key]['siparis_no'])->get();
                    if(in_array('count',$includes)){
                        $siparis[$key]["count"] = count($urunler);
                    }
                    if(in_array('urunler',$includes)){
                        if(is_object($urunler) && count($urunler) > 0){
                            if(in_array('urun',$includes)){
                                foreach ($urunler as $key2 => $value2) {
                                    $urun = Products::where('id','=',$value2['urun_id'])->get();
                                    if(is_object($urun) && count($urun) > 0){
                                        $urunler[$key2]['urun'] = $urun[0];
                                    }
                                }
                            }
                            $siparis[$key]['urunler'] = $urunler;
                        }
                    }
                }
            }

            $response = response()->json(['data' => $siparis,'status'=> 200,'created_at' => date('Y-m-d h:i:s',time())],200);
            Cache::put('siparis/'.$id.'/'.$include,$response, $seconds = 180);
            return $response;

        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);   
        }
    }
    public function siparis(Request $request, $id, $sip)
    {
        $include = "null";

        $includes = array();
        if($request->has('include')){
            $include = $request->get('include');
            $includes = explode('|', $include);
        }

        if(Cache::has('siparisdet/'.$id.'/'.$sip.'/'.$include)){
            $value = Cache::get('siparisdet/'.$id.'/'.$sip.'/'.$include);
            return $value;
        }

        $siparis = Siparis::where('user_id',$id)->where('id',$sip)->get();
        if(is_object($siparis) && count($siparis)> 0){
            if(in_array('urunler',$includes) || in_array('count',$includes)){
                foreach ($siparis as $key => $value) {
                    $urunler = SiparisUrunler::where('siparis_id',$siparis[$key]['siparis_no'])->get();
                    if(in_array('count',$includes)){
                        $siparis[$key]["count"] = count($urunler);
                    }
                    if(in_array('urunler',$includes)){
                        if(is_object($urunler) && count($urunler) > 0){
                            if(in_array('urun',$includes)){
                                foreach ($urunler as $key2 => $value2) {
                                    $urun = Products::where('id','=',$value2['urun_id'])->get();
                                    if(is_object($urun) && count($urun) > 0){
                                        $urunler[$key2]['urun'] = $urun[0];
                                    }
                                }
                            }
                            $siparis[$key]['urunler'] = $urunler;
                        }
                    }
                }
            }
            $response = response()->json(['data' => $siparis,'status'=> 200,'created_at' => date('Y-m-d h:i:s',time())],200);
            Cache::put('siparisdet/'.$id.'/'.$sip.'/'.$include, $response, $seconds = 300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);   
        }
    }
    public function create(Request $request,$id)
    {
        if(Cache::has('cresip/'.$id)){
            if(Cache::get('cresip/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık sipariş oluşturdunuz.'],404);
            }else{
                Cache::increment('cresip/'.$id,1, $seconds = 30);
            }
        }else Cache::put('cresip/'.$id, 1 , $seconds = 30);

        $req = $request->all();
        $req['user_id'] = $id;
        $validator = Validator::make(
            $req,
            [
                'ara_tutar' => 'required|numeric',
                'kdv_tutar' => 'required|numeric',
                'kargo_tutar' => 'required|numeric',
                'toplam_tutar' => 'required|numeric',
                'isim' => 'required|alpha',
                'soyisim' => 'required|alpha',
                'tel' => 'required|digits:11|numeric',
                'eposta' => 'required|email',
                'adres' => 'required',
                'adres_fatura' => 'nullable',
                'sehir' => 'required',
                'postakodu' => 'required|numeric',
                'notlar' => 'max:500',
                'odeme_tip' => 'required|numeric|max:2',
                'siparis_id' => 'nullable|numeric',
                'siparis_tarih' => 'required|date',
                'siparis_no' => 'required|unique:siparis|numeric',
                'siparis_durum' => 'required|numeric|max:2',
                'user_id' => 'required|numeric',
            ],
            [
                'ara_tutar.required' => "Ara tutar alanı mecburidir.",
                'kdv_tutar.required' => "KDV tutar alanı mecburidir.",
                'kargo_tutar.required' => "Kargo tutar alanı mecburidir.",
                'toplam_tutar.required' => "Toplam tutar alanı mecburidir.",
                'isim.required' => "İsim alanı mecburidir.",
                'soyisim.required' => "Soyisim alanı mecburidir.",
                'tel.required' => "Telefon alanı mecburidir.",
                'eposta.required' => "E-posta alanı mecburidir.",
                'adres.required' => "Adres alanı mecburidir.",
                'adres_fatura.required' => "Fatura Adresi alanı mecburidir.",
                'sehir.required' => "Şehir alanı mecburidir.",
                'postakodu.required' => "Posta kodu alanı mecburidir.",
                'odeme_tip.required' => "Ödeme Tip alanı mecburidir.",
                'siparis_tarih.required' => "Sipariş tarihi alanı mecburidir.",
                'siparis_no.required' => "Sipariş numarası alanı mecburidir.",
                'siparis_durum.required' => "Sipariş durumu alanı mecburidir.",
                'user_id.required' => "Kullanıcı ID alanı mecburidir.",

                'ara_tutar.numeric' => "Lütfen sadece rakam kullanınız.",
                'kdv_tutar.numeric' => "Lütfen sadece rakam kullanınız.",
                'kargo_tutar.numeric' => "Lütfen sadece rakam kullanınız.",
                'toplam_tutar.numeric' => "Lütfen sadece rakam kullanınız.",
                'isim.alpha' => "Lütfen geçerli bir isim giriniz.",
                'soyisim.alpha' => "Lütfen geçerli bir soyisim giriniz.",
                'tel.numeric' => "Lütfen sadece rakam kullanınız.",
                'eposta.email' => "Lütfen bir mail adresi giriniz.",
                'postakodu.numeric' => "Lütfen sadece rakam kullanınız.",
                'odeme_tip.numeric' => "Lütfen sadece rakam kullanınız.",
                'siparis_id.numeric' => "Lütfen sadece rakam kullanınız.",
                'siparis_tarih.date' => "Lütfen geçerli bir tarih giriniz.",
                'siparis_no.numeric' => "Lütfen sadece rakam kullanınız.",
                'siparis_durum.numeric' => "Lütfen sadece rakam kullanınız.",
                'user_id.numeric' => "Lütfen sadece rakam kullanınız.",

                'tel.digits' => "Lütfen geçerli bir telefon numarası giriniz.",
                'notlar.max' => "500 karakterden fazla not iletilmez.",
                'odeme_tip.max' => "Lütfen geçerli bir ödeme tipi giriniz. (1,2)",
                'siparis_durum.max' => "Lütfen geçerli bir sipariş durumu giriniz. (0,1,2,3,4,5,6)",

                'siparis_no.unique' => "Bu sipariş numarasıyla daha önce sipariş oluşturuldu",
            ]
        );
        if($validator->fails()){
             return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $req['isim'] = $req['isim'].' '.$req['soyisim'];
            unset($req['soyisim']);
            $siparis = Siparis::create($req);

            if($req['odeme_tip'] == 1){
                app(MailController::class)->havale_siparis_musteri($request,$req['eposta'],$req['isim'],$req['siparis_no'],$req['toplam_tutar'],$req['tel']);
            }else if($req['odeme_tip'] == 2){
                app(MailController::class)->kk_siparis_musteri($request,$req['eposta'],$req['isim'],$req['siparis_no'],$req['toplam_tutar'],$req['tel']);
            }

            return response()->json(['data' => $siparis,'status'=> 200,'created_at' => date('Y-m-d h:i:s',time())],200);
        }
    }
    public function create_product(Request $request,$id,$sid)
    {
        $req = $request->all();
        $req['siparis_id'] = $sid;
        $validator = Validator::make(
            $req,
            [
                'urun_id' => 'required|numeric',
                'siparis_id' => 'required|numeric',
                'urun_baslik' => 'required',
                'adet' => 'required|numeric',
                'tutar' => 'required',
                'kdv_tutar' => 'nullable',
                'kargo_tutar' => 'nullable',
                'varyantlar' => 'nullable',
            ],
            [
                'urun_id.required' => 'Urun Id alanı mecburidir.',
                'urun_id.numeric' => 'Urun Id sayı olmalıdır.',
                'siparis_id.required' => 'Sipariş ID alanı mecburidir.',
                'siparis_id.numeric' => 'Sipariş ID alanı sayı olmalıdır',
                'urun_baslik.required' =>  'Ürün ismi alanı mecburidir.',
                'adet.required' => 'Ürün adeti alanı mecburidir.',
                'adet.numeric' => 'Ürün adeti sayı olmalıdır.',
                'tutar.required'=> 'Tutar alanı mecburidir.',
            ],
        );
        if($validator->fails()){
             return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }else{
            $urun = SiparisUrunler::create($req);


            //TODO adet düşürülecek
            return response()->json(['data' => $urun,'status'=> 200,'created_at' => date('Y-m-d h:i:s',time())],200);
        }
    }
}
