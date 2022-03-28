<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;



class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $filters = "all";

        if($request->has('filter')){
            $filters = $request->get('filter');
        }

        if(Cache::has('campaigns/'.$filters)){
            $value = Cache::get('campaigns/'.$filters);
            return $value;
        }

        if($filters == 0){
            $data = Campaign::where('durum',1)->where('dil','tr')->where('icon',0)->get();
        }else if($filters == 1){
            $data = Campaign::where('durum',1)->where('dil','tr')->where('icon',1)->get();
        }else{
            $data = Campaign::where('durum',1)->where('dil','tr')->get();            
        }

        if(count($data) > 0){
            $response = response()->json([
                'data' => $data,
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time()),
            ],200);
        }else{
            $response = response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
        Cache::put('campaigns/'.$filters,$response, $seconds = 120);
        return $response;
    }

    public function show(Request $request,$id)
    {
        if(Cache::has('campaign/'.$id)){
            $value = Cache::get('campaign/'.$id);
            return $value;
        }

        $data = Campaign::where('id',$id)->where('dil','tr')->get();
        if (count($data) > 0) {
            $response = response()->json([
                'data' => $data[0],
                'status'=>200,
                'created_at' => date('Y-m-d h:i:s',time()),
            ],200);

            Cache::put('campaign/'.$id,$response, $seconds = 120);
            return $response;
        }else{
            return response()->json(['message' => 'Kayıt Bulunamadı','status' => 404],404);
        }
    }
}
