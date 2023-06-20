<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ErrorController extends Controller
{
    // 使用者不存在、帳密有誤 
    public static function InvalidLogin() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_INVALID_LOGIN',
        ], 403);
    }
    // body或query資料格式錯誤 
    public static function WrongDataType() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_WROND_DATA_TYPE',
        ], 400);
    }
    // body或query缺少必要欄位 
    public static function MissingField() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_MISSING_FIELD',
        ], 400);
    }
    // 密碼長度不正確
    public static function PasswordNotSecure() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_PASSWORD_NOT_SECURE',
        ], 409);
    }
    // 使用者已存在 
    public static function UserExists() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_USER_EXISTS',
        ], 409);
    }
    // 無效的Access token 
    public static function InvalidAccessToken() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_INVALID_ACCESS_TOKEN',
        ], 401);
    }
    // 檔案格式錯誤
    public static function InvalidFileFormat() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_INVALID_FILE_FORMAT',
        ], 400);
    }
    // 不存在的圖片
    public static function PostNotExists() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_POST_NOT_EXISTS',
        ], 404);
    }
    // 權限不足
    public static function PermissionDeny() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_PERMISSION_DENY',
        ], 403);
    }
    // 不存在的評論
    public static function CommentNotExists() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_COMMENT_NOT_EXISTS',
        ], 404);
    }
    // 不存在的使用者
    public static function UserNotExists() {
        return response()->json([
            'success' => FALSE,
            'message' => 'MSG_USER_NOT_EXISTS ',
        ], 404);
    }
}
