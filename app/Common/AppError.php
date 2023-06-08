<?php

namespace App\Common;


class AppError {
    const NO_ERROR = 0;

    // Common
    const INVALID_REQUEST = 1;
    const INVALID_FORMAT = 2;
    const ALREADY_EXIST = 3;
    const NOT_FOUND = 4;
    const UNKNOWN_USER = 5;

    // Database
    const DB_UNHANDLED_EXCEPTION = 11;
    const DB_DUPLICATED_ENTRY = 12;

    // Game server
    const CANNOT_CONNECT_TO_GAME_SERVER = 21;
    const ACCOUNT_NOT_BOUND = 22;
    const ACCOUNT_NOT_EXIST = 23;
    const INVALID_VOUCHER_CODE = 24;
    const ALREADY_USED_VOUCHER_CODE = 25;
    const UNEXPECTED_GAME_SERVER_RESPONSE = 26;
    const INVALID_PRESENT_ID = 27;
    const ALREADY_GOT_THE_PRESENT = 28;
    const SET_PRESENT_FAILED = 29;
    const INVALID_BONUS_LIST = 30;
    const ALREADY_GOT_THE_BONUS = 31;
    const SET_BONUS_FAILED = 32;
    const GET_PRESENT_FAILED = 33;

    // Event condition
    const EVENT_CONDITION_NOT_SATISFIED = 41;
    const UNKNOWN_EVENT_CONDITION = 42;

    // Lottery
    const THE_ACTIVITY_NOT_START = 51;
    const THE_ACTIVITY_ALREADY_END = 52;
    const ALREADY_PARTICIPATED = 53;
    const CANNOT_REDEEM_THE_REWARD = 54;

    // Vote
    const EXIST_TIECKT_NOT_MATCH = 61;
    const NO_AVAILABLE_TICKET = 62;
    const TICKET_NOT_ENOUGH = 63;
    const TIKCET_NUMBER_MISMATCH = 64;
    const CHARACTER_NOT_EXIST = 65;
    const INVALID_VOTE_ACCOUNT = 66;
    const INVALID_VOTE_ACCOUNT_ID = 67;
    const NOT_BIND_WITH_ANY_GAMES = 68;
    const ACTIVITY_END = 69;
    const VOTE_ACCOUNT_NOT_CREATED = 70;
    const ACTIVITY_NOT_START = 71;

    // Send message
    const LINEBOT_UNHANDLED_EXCEPTION = 71;
    const INVALID_LINEBOT_TOKEN = 72; // http code 401
    const INVALID_MESSAGE_FORMAT = 73; // http code 400
    const REACHED_MONTHLY_LIMIT = 74; // http code 429

    // Script
    const ANOTHER_SCRIPT_IS_WAITING_REPLY = 81;
    const INVALID_SCRIPT_STATUS = 82;

    const UNKNOW_ERROR = 9999;
}
