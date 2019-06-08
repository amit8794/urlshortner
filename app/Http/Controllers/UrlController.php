<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\url_shortener;

class UrlController extends Controller
{
    /**
     * accept long url and return short url
     *
     * 
     * @return json
     */
    public function short() {
        $long_url = Input::get('long_url');
        if (!isset($long_url) || empty($long_url)) {
            return response()->json("please pass url to short");
        }
        if (!filter_var($long_url, FILTER_VALIDATE_URL)) {
            return response()->json('Not a valid URL');
        }
        $short_code = url_shortener::where('long_url', $long_url)->value('short_code');
        $base_url =  url('/');
        if(empty($short_code)){
            $token = substr(md5(uniqid(rand(), true)),0,6);
            url_shortener::insert(
                ['short_code' => $token, 'long_url' => $long_url,'hits' => 0]
            );
            return response()->json($base_url.'/'.$token);
        }else{
            return response()->json($base_url.'/'.$short_code);
        }
        
    }
    /**
     * accept short code url and redirect to long url
     *
     * 
     *  @return void
     */
    public function redirect($id) {
        $long_url = url_shortener::where('short_code', $id)->value('long_url');
        if(empty($long_url)){
            return response()->json("short code not found");
        }else{
            return Redirect::away($long_url);
        }
        
    }
}
