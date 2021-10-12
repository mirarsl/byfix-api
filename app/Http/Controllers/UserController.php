<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function show(Request $request, $id)
    {
        if(Cache::has('user/'.$id)){
            $value = Cache::get('user/'.$id);
            return $value;
        }   

        $article = User::where('id',$id)->get();
        if (count($article) > 0) {
            $response = response()->json([
                'data' => $article,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time())
            ],200);
            Cache::put('user/'.$id,$response,$seconds=300);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }

    public function login(Request $request)
    {
        if(Cache::has('login_attempt/'.$request->get('email'))){
            if(Cache::get('login_attempt/'.$request->get('email')) >= 3){
                return response()->json(['error'=> 'Çok sık giriş yapmayı denediniz.'],404);
            }else{
                Cache::increment('login_attempt/'.$request->get('email'),1,$seconds = 10);
            }
        }else Cache::put('login_attempt/'.$request->get('email'), 1 , $seconds = 10);

        $validator = Validator::make(
            [
                'eposta' => $request->get('email'),
                'uyesifre' => $request->get('password'),
            ],
            [
                'eposta' => 'required|email',
                'uyesifre' => 'required|min:6',
            ],
            [
                'eposta.required' => "E-Posta alanı mecburidir.",
                'eposta.email' => "Lütfen bir mail adresi giriniz.",
                'uyesifre.required' => "Şifre alanı mecburidir.",
                'uyesifre.min' => "Şifre minumum 6 karakter olmalıdır."
            ],
        );
        if(!$validator->fails()){
            $user = User::where('eposta',$request->get('email'))->get();
            if(count($user) > 0){
                if($user[0]['uyesifre'] == md5($request->get('password'))){
                    $response =  response()->json([
                        'data' => $user,
                        'status'=>200,
                        'created_at' => date('Y-m-d h:i:s',time())
                    ],200);                    
                    return $response;
                }
                else{
                    return response()->json(["message"=>"Şifre Yanlış",'status'=>404],404);
                }
            }else{
                return response()->json(["message"=>"Kayıtlı Kullanıcı Bulunamadı",'status'=>404],404);
            }
        }else{
            return response()->json(["message"=>$validator->messages(),'status'=>404],404);
        }
    }
    public function register(Request $request)
    {
        $ip = $request->ip();

        if(Cache::has('register_attempt/'.$ip)){
            if(Cache::get('register_attempt/'.$ip) >= 1){
                return response()->json(['error'=> 'Çok sık kayıt olmayı denediniz.'],404);
            }else{
                Cache::increment('register_attempt/'.$ip,1, $seconds = 60);
            }
        }else Cache::put('register_attempt/'.$ip, 1 , $seconds = 60);


        $validator = Validator::make(
            [
                'isim' =>$request->get('name'),
                'soyisim' =>$request->get('surname'),
                'telefon' =>$request->get('phone'),
                'eposta' => $request->get('email'),
                'cinsiyet' => $request->get('gender'),
                'tcno' => $request->get('tcno'),
                'uyesifre' => $request->get('password'),
                'uyesifre_reply' => $request->get('password_reply'),
                'ip' => $ip,
                'tarih' => date('Y-m-d',time()),
            ],
            [
                'isim' => "required|alpha|max:255",
                'soyisim' => "required|alpha|max:255",
                'telefon' => 'required|digits:11|numeric',
                'eposta' => 'required|email|unique:uyeler',
                'cinsiyet' => 'required|alpha|max:10',
                'tcno' => 'required|numeric|digits:11|unique:uyeler',
                'uyesifre' => 'required|min:6',
                'uyesifre_reply' => 'required|min:6|same:uyesifre',
            ],
            [
                'isim.required' => "İsim alanı mecburidir.",
                'isim.alpha' => "Lütfen geçerli bir isim giriniz.",
                'soyisim.required' => "Soyisim alanı mecburidir.",
                'soyisim.alpha' => "Lütfen geçerli bir soyisim giriniz.",
                'telefon.required' => "Telefon alanı mecburidir",
                'telefon.digits' => "Telefon numarası 11 haneli olmalıdır.",
                'telefon.numeric' => "Lütfen sadece rakam kullanınız.",
                'eposta.required' => "E-Posta alanı mecburidir.",
                'eposta.email' => "Lütfen bir mail adresi giriniz.",
                'eposta.unique' => "Bu e-posta adresi daha önce kullanıldı.",
                'cinsiyet.required' => "Cinsiyet alanı mecburidir.",
                'cinsiyet.alpha' => "Lütfen geçerli bir cinsiyet seçiniz.",
                'tcno.required' => "T.C No alanı mecburidir.",
                'tcno.numeric' => "Lütfen geçerli bir T.C Numara giriniz.",
                'tcno.digits' => "T.C Numarası 11 haneli olmalıdır.",
                'tcno.unique' => "Bu T.C numarası ile daha önce kayıt oldunuz.",
                'uyesifre.required' => "Şifre alanı mecburidir.",
                'uyesifre.min' => "Şifre minumum 6 karakter olmalıdır.",
                'uyesifre_reply.required' => "Şifre tekrarı alanı mecburidir.",
                'uyesifre_reply.min' => "Şifre tekrarı minumum 6 karakter olmalıdır.",
                'uyesifre_reply.same' => "Şifreler birbirleri ile aynı değil.",
            ],
        );
        if($validator->fails()){
            return response()->json(["message"=>$validator->messages(),'status'=>300],300);
        }else{
            $user = User::create([
                'isim' => $request->get('name'),
                'soyisim' =>$request->get('surname'),
                'telefon' =>$request->get('phone'),
                'eposta' => $request->get('email'),
                'cinsiyet' => $request->get('gender'),
                'tcno' => $request->get('tcno'),
                'uyesifre' => md5($request->get('password')),
                'uyesifre_reply' => md5($request->get('password_reply')),
                'ip' => $ip,
                'tarih' => date('Y-m-d',time()),
            ]);
            app(MailController::class)->register($request,$user['eposta'],$user['isim'].' '.$user['soyisim']);

            return response()->json(['data' => $user,'status'=> 200],200);
        }
    }
    public function password(Request $request,$id)
    {

        if(Cache::has('password_attempt/'.$id)){
            if(Cache::get('password_attempt/'.$id) >= 1){
                return response()->json(['error'=> 'Çok sık şifre değiştirmeyi denediniz.'],404);
            }else{
                Cache::increment('password_attempt/'.$id,1, $seconds = 60);
            }
        }else Cache::put('password_attempt/'.$id, 1 , $seconds = 60);

        $user = User::where('id',$id)->get();
        if(count($user) > 0){
            $validator = Validator::make(
                [
                    'uyesifre' => $request->get('old_password'),
                    'new_uyesifre' => $request->get('new_password'),
                    'new_uyesifre_reply' => $request->get('new_password_reply'), 
                ],
                [
                    'uyesifre' => ['required','min:6', function($attribute, $value, $fail) use ($user){
                        if (md5($value) != $user[0]['uyesifre']) {
                            return $fail('Mevcut şifre yanlış girildi.');
                        }
                    }],
                    'new_uyesifre' => 'required|min:6|different:uyesifre',
                    'new_uyesifre_reply' => 'required|min:6|same:new_uyesifre',

                ],
                [
                    'uyesifre.required' => "Şifre alanı mecburidir.",
                    'uyesifre.min' => "Şifre minumum 6 karakter olmalıdır.",
                    'new_uyesifre.required' => "Şifre tekrarı alanı mecburidir.",
                    'new_uyesifre.min' => "Şifre tekrarı minumum 6 karakter olmalıdır.",
                    'new_uyesifre.different' => "Şifreniz önceki şifreniz ile aynı olamaz.",
                    'new_uyesifre_reply.required' => "Şifre tekrarı alanı mecburidir.",
                    'new_uyesifre_reply.min' => "Şifre tekrarı minumum 6 karakter olmalıdır.",
                    'new_uyesifre_reply.same' => "Şifreler birbirleri ile aynı değil.",
                ]
            );
            if ($validator->fails()) {
                return response()->json(["message"=>$validator->messages(),'status'=>300],300);
            }else{
                User::where('id', $id)->update([
                    'uyesifre' => md5($request->get('new_password')),
                ]);
                $new = User::where('id',$id)->get();
                return response()->json(['data' => $new,'status'=> 200],200);
            }
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
        
    }
}
