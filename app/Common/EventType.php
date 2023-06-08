<?php

namespace App\Common;

class EventType {
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const LOGIN_FAILED = 'login_failed';
    const VERIFICATION_CODE_CHECK_FAILED = 'verification_code_check_failed';
    const DOWNLOAD_USER_LIST = 'download_user_list';
    const DOWNLOAD_USER_EVENT = 'download_user_event';
    const DOWNLOAD_THEME_EXHIBITION_RESERVATION = 'download_theme_exhibition_reservation';
    const CHANGE_PASSWORD = 'change_password';
}
