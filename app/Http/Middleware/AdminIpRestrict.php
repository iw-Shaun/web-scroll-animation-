<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Common\AppError;

class AdminIpRestrict
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('services.admin.adminIpRestrict')) {
            // Check ip.
            $clientIP = getClientIp();
            $ips = Client::select(['ip'])->get()->pluck('ip')->toArray();
            if (!in_array($clientIP, $ips)) {
                Log::debug("Invalid IP: {$clientIP}");
                return response()->json(['code' => AppError::INVALID_SOURCE_ID, 'message' => 'Invalid source IP'], 403);
            }
        }

        return $next($request);
    }
}
