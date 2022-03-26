<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Categories;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $term = "";

        if($request->has('term')){
            $term = $request->get('term');
        }

        if(Cache::has('search/'.$term)){
            $value = Cache::get('search/'.$term);
            return $value;
        }

        $products = Products::select()->where('dil','tr')->where('baslik','like','%'.$term.'%')->orWhere('spot','like','%'.$term.'%')->orWhere('icerik','like','%'.$term.'%')->orWhere('tags','like','%'.$term.'%')->addSelect(DB::raw("'product' as type"))->get();

        $categories = Categories::select()->where('dil','tr')->where('baslik','like','%'.$term.'%')->where('tags','like','%'.$term.'%')->addSelect(DB::raw("'category' as type"))->get();

        $data = [
            'products' => $products,
            'categories' => $categories
        ];
        $response = response()->json([
            'data' => $data,
            'status'=>200,
            'created_at' => date('Y-m-d h:i:s',time())
        ],200);

        Cache::put('search/'.$term, $response, $seconds = 300);
        return $response;
    }
}
