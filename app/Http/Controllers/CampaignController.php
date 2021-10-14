<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;



class CampaignController extends Controller
{
    public function index(Request $request)
    {
        if(Cache::has('campaigns')){
            $value = Cache::get('campaigns');
            return $value;
        }
        $data = Campaign::where('durum',1)->get();
        $response = response()->json([
            'data' => $data,
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time()),
        ],200);

        Cache::put('campaigns',$response, $seconds = 120);
        return $response;
    }

    public function show(Request $request,$id)
    {
        if(Cache::has('campaign/'.$id)){
            $value = Cache::get('campaign/'.$id);
            return $value;
        }

        $data = Campaign::where('id',$id)->get();
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
