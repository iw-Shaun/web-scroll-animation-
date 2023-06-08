<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminEvent;

class EventController extends Controller
{
    const VERIFICATION_CODE_RESEND_GAP_TIME = 30;
    const VERIFICATION_CODE_EXPIRED_TIME = 300; // 5 min

    public function getEvents(Request $request)
    {
        $pageNum = (int)$request->post('page_num');
        $pageSize = 20;
        $totalNum = AdminEvent::count();
        $totalPage = ceil($totalNum / $pageSize);
        if ($pageNum < 1 || $pageNum > $totalPage) {
            $pageNum = 1;
        }
        $skipNum = ($pageNum - 1) * $pageSize;

        $events = AdminEvent::with(['admin' => function($q) {
            $q->select(['id', 'email']);
        }])->orderBy('id', 'DESC')->skip($skipNum)->take($pageSize)->get();

        $res = [
            'data' => $events,
            'page_num' => $pageNum,
            'page_size' => $pageSize,
            'total_num' => $totalNum,
        ];
        return response()->json($res);
    }

    public static function addEvent($adminId, $eventType)
    {
        $event = new AdminEvent();
        $event->admin_id = $adminId;
        $event->event = $eventType;
        $event->ip = getClientIp();
        $event->save();
    }
}
