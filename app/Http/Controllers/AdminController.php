<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EventController;
use App\Models\Admin;
use App\Models\AdminEvent;
use App\Common\AppError;
use App\Common\EventType;
// use App\Common\AdminRole;

class AdminController extends Controller
{
    const VERIFICATION_CODE_RESEND_GAP_TIME = 30;
    const VERIFICATION_CODE_EXPIRED_TIME = 300; // 5 min

    public function me(Request $request)
    {
        $admin = $request->get('admin')->toArray();
        $admin = array_intersect_key($admin, array_flip(['id', 'email', 'name', 'role']));
        return response()->json(['data' => $admin]);
    }

    /**
     * Have to set tmp_admin_id to session.
     */
    public function login(Request $request)
    {
        if (config('services.admin.enablePassowrdLogin') != true) {
            return response()->json(['code' => AppError::PASSWORD_LOGIN_IS_DISABLED], 400);
        }

        $email = $request->post('email');

        // Check the failed times.
        $durationMinutes = config('services.admin.loginFailedDurationMinutes');
        $targetTs = strtotime("-{$durationMinutes} minutes");
        $targetDate = date('Y-m-d H:i:s', $targetTs);
        $failedCount = AdminEvent::whereHas('admin', function($q) use ($email) {
            $q->where('email', $email);
        })->where([
            ['event', EventType::LOGIN_FAILED],
            ['created_at', '>=', $targetDate],
        ])->count();
        // If the same email login failed multiple times, then blocking for a while.
        $limitTimes = config('services.admin.loginFailedTimesLimit');
        if ($failedCount >= $limitTimes) {
            return response()->json(['code' => AppError::LOGIN_FAILED_COUNT_LIMIT], 400);
        }

        $admin = Admin::where('email', $email)->first();
        if (!$admin) {
            return response()->json(['code' => AppError::INVALID_USER_OR_PASSWORD], 400);
        }
        if (!Hash::check($request->post('password'), $admin->password)) {
            EventController::addEvent($admin->id, EventType::LOGIN_FAILED);
            return response()->json(['code' => AppError::INVALID_USER_OR_PASSWORD], 400);
        }

        // Check if the account is inactive.
        // if ($admin->loggedin_at) {
        //     $diff = time() - strtotime($admin->loggedin_at);
        //     if ($diff > 0 && $diff > config('services.admin.accountInactiveDays') * 86400) {
        //         return response()->json(['code' => AppError::ACCOUNT_IS_INACTIVE], 400);
        //     }
        // }

         // Set admin_id for AdminAuth class.
        $request->session()->put('admin_id', $admin->id);

        // Clean the code.
        $admin->verification_code = null;
        $admin->verification_code_generated_at = null;
        $admin->verification_code_failed_times = 0;

        $admin->loggedin_at = date('Y-m-d H:i:s');

        $admin->save();

        EventController::addEvent($admin->id, EventType::LOGIN);

        return response()->json(['data' => 'Success']);
    }

    public function loginFail(Request $request)
    {
        $html = '<div>' . ($request->input('message') ?? 'Login failed.') . '</div>';
        $html .= '<div><a href="/admin/login">Login</a></div>';
        return response($html, 400);
    }

    public function logout(Request $request)
    {
        $admin = $request->get('admin');

        $request->session()->forget('admin_id');

        // Log event.
        EventController::addEvent($admin->id, EventType::LOGOUT);

        return response()->json(['code' => AppError::NO_ERROR]);
    }

    public function changePassword(Request $request)
    {
        $admin = $request->get('admin');

        $oldPassword = $request->post('old_password');
        $newPassword = $request->post('new_password');
        if (empty($oldPassword) || empty($newPassword)) {
            return response()->json(['code' => AppError::INVALID_REQUEST], 400);
        }

        if (!Hash::check($oldPassword, $admin->password)) {
            return response()->json(['code' => AppError::INVALID_PASSWORD], 400);
        }

        // Check new password format.
        if (strlen($newPassword) < 10) {
            return response()->json(['code' => AppError::PASSWORD_FORMAT_ERROR], 400);
        }
        if (!preg_match('`[^0-9a-zA-Z]`', $newPassword)) {
            return response()->json(['code' => AppError::PASSWORD_FORMAT_ERROR], 400);
        }
        if (!preg_match('`[a-zA-Z]`', $newPassword)) {
            return response()->json(['code' => AppError::PASSWORD_FORMAT_ERROR], 400);
        }

        $admin->password = bcrypt($newPassword);
        $admin->password_updated_at = date('Y-m-d H:i:s');
        $admin->save();

        EventController::addEvent($admin->id, EventType::CHANGE_PASSWORD);

        return response()->json(['data' => 'Success']);
    }

    public function getAdmins(Request $request)
    {
        $admins = Admin::select(['id', 'email', 'name', 'role', 'active'])->get();
        return response()->json(['data' => $admins]);
    }

    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            // 'role' => ['required', Rule::in([AdminRole::Administrator])],
            'active' => ['required', 'integer', 'between:0,1'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid format'], 400);
        }

        $admin = new Admin;
        $admin->email = trim(strtolower($request->post('email')));
        $admin->password = bcrypt(Str::random(32));
        $admin->name = $request->post('name');
        $admin->role = $request->post('role');
        $admin->active = $request->post('active');
        $admin->save();

        return response()->json(['data' => $admin]);
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['code' => AppError::NOT_FOUND], 400);
        }

        // Update
        $admin->name = $request->post('name');
        $admin->role = $request->post('role');
        $admin->active = $request->post('active');
        $admin->save();

        return response()->json(['data' => 'Success']);
    }

    public function removeAdmin(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['code' => AppError::NOT_FOUND], 400);
        }
        $admin->delete();
        return response()->json(['data' => 'Success']);
    }
}
