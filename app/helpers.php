<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

function vite_assets(): HtmlString
{
    $devServerIsRunning = false;
    
    if (app()->environment('local')) {
        try {
            Http::get("http://localhost:5173");
            $devServerIsRunning = true;
        } catch (Exception) {
        }
    }
    
    if ($devServerIsRunning) {
        return new HtmlString(<<<HTML
            <link rel="stylesheet" href="http://localhost:5173/resources/sass/app.scss" type="text/css" />
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
        HTML);
    }
    
    $manifest = json_decode(file_get_contents(
        public_path('build/manifest.json')
    ), true);
    
    return new HtmlString(<<<HTML
        <script type="module" src="/build/{$manifest['resources/js/app.js']['file']}"></script>
        <link rel="stylesheet" href="/build/{$manifest['resources/sass/app.scss']['file']}">
    HTML);
}

function getClientIp()
{
    $clientIP = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // To check ip is pass from proxy
        $clientIPs = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $clientIPs = array_map('trim', $clientIPs);
        $clientIP = end($clientIPs); // Get the last IP which create by load balancer, don't get the first, it may fake by client.
    } else {
        $clientIP = $_SERVER['REMOTE_ADDR'];
    }

    // Remove port part.
    $parts = explode(':', $clientIP);
    $clientIP = reset($parts);
    return $clientIP;
}