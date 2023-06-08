<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log; //test log

class RootController extends Controller
{
    public function show(Request $request)
    {
        $share =  $request->query('share','1');
        //Log::info('==share== '.$share); //debug
        //Log::info('==deepLink== '.config("services.cresclab.deepLink.{$share}")); //debug

        return view('react', [
            'appUrl' => config('app.url'),
            'assetUrl' => rtrim(secure_asset('/'), '/'),
            'appVer' => config('app.version'),
            'useMockUser' => config('services.line.useMockUser'),
            'oaUrl' => config('services.line.oaUrl'),
            'liffId' => config('services.line.liffId'),
            'liffUrl' => config('services.line.liffUrl'),
            'loginChannelId' => config('services.line.loginChannelId'),
            'enPwdLogin' => config('services.admin.enablePassowrdLogin'),
            'enSamlLogin' => config('services.admin.enableSamlLogin'),
            'gtagId' => config('services.ga.gtagId'),
        ]);
    }
}
