<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Mail;


class MailController extends Controller {
   
   public function havale_siparis_satici(Request $request, $name, $email, $sip, $tel) {
      $data = array(
        'name'=>$name,
        'return_email' => 'noreply@byfix.com.tr',
        'email' => $email,
        'sip'=>$sip,
        'tel' => $tel,
    );
   
      $status = Mail::send(['html'=>'havale_siparis_satici'], $data, function($message) {
         $message->to("info@byfix.com.tr", "Byfix Store | Better Like This")->subject('Yeni Bir Sipariş Var!');
         $message->from('noreply@byfix.com.tr','Byfix Store | Better Like This');
      });
      return response()->json(['status'=> 200],200);
   }

   public function havale_siparis_musteri(Request $request, $email, $name, $sip,$tutar,$tel) {
      $data = array(
        'name'=>$name,
        'return_email' => 'noreply@byfix.com.tr',
        'sip'=>$sip,
        'tutar' => number_format($tutar,2)
    );

      Mail::send(['html'=>'havale_siparis_musteri'], $data, function($message) use($email,$name) {
         $message->to($email, $name)->subject('Siparişiniz Bize Ulaştı');
         $message->from('noreply@byfix.com.tr','Byfix Store | Better Like This');
      });

      $this->havale_siparis_satici($request, $name, $email, $sip, $tel);
      return response()->json(['status'=> 200],200);
   }

   public function kk_siparis_satici(Request $request, $name, $email, $sip, $tel) {
      $data = array(
        'name'=>$name,
        'return_email' => 'noreply@byfix.com.tr',
        'email' => $email,
        'sip'=>$sip,
        'tel' => $tel,
    );
   
      $status = Mail::send(['html'=>'kk_siparis_satici'], $data, function($message) {
         $message->to("info@byfix.com.tr", "Byfix Store | Better Like This")->subject('Yeni Bir Sipariş Var!');
         $message->from('noreply@byfix.com.tr','Byfix Store | Better Like This');
      });
      return response()->json(['status'=> 200],200);
   }

   public function kk_siparis_musteri(Request $request, $email, $name, $sip,$tutar,$tel) {
      $data = array(
        'name'=>$name,
        'return_email' => 'noreply@byfix.com.tr',
        'sip'=>$sip,
        'tutar' => number_format($tutar,2)
    );

      Mail::send(['html'=>'kk_siparis_musteri'], $data, function($message) use($email,$name) {
         $message->to($email, $name)->subject('Siparişiniz Bize Ulaştı');
         $message->from('noreply@byfix.com.tr','Byfix Store | Better Like This');
      });

      $this->havale_siparis_satici($request, $name, $email, $sip, $tel);
      return response()->json(['status'=> 200],200);
   }

   public function register(Request $request, $email, $name)
   {
     $data = array(
        'name'=>$name,
        'return_email' => 'noreply@byfix.com.tr',
    );

     Mail::send(['html'=>'register'], $data, function($message) use($email,$name) {
       $message->to($email, $name)->subject('Yeni Üyelik Oluşturuldu');
       $message->from('noreply@byfix.com.tr','Byfix Store | Better Like This');
    });
     return response()->json(['status'=> 200],200);
   }

}