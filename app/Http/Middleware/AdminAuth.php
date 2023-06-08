<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin;
use App\Common\AppError;

class AdminAuth
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
        $adminId = $request->session()->get('admin_id');
        if (!$adminId) {
            return response()->json(['code' => AppError::PERMISSION_DENIED], 403);
        }

        $admin = Admin::find($adminId);
        if ($admin === null) {
            return response()->json(['code' => AppError::PERMISSION_DENIED], 403);
        }

        // Check the active flag.
        if ($admin->active == false) {
            return response()->json(['code' => AppError::ACCOUNT_IS_INACTIVE, 'message' => 'This account is inactive.'], 400);
        }

        // Check role.
        // if (empty($admin->role)) {
        //     return response()->json(['code' => AppError::INVALID_ROLE], 403);
        // }

        // Set to request.
        $request->attributes->add(['admin' => $admin]);

        // Set the session timeout.
        $adminSessionLifetime = config('services.admin.session_lifetime');
        config(['session.lifetime' => $adminSessionLifetime]);

        return $next($request);
    }
}
