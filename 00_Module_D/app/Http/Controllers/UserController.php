<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ErrorController;

class UserController extends Controller
{
    //登入
    public function login(Request $request) {
        // body或query缺少必要欄位 
        if (!$request->has(['email', 'password'])) {
            return ErrorController::MissingField();
        }
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // body或query資料格式錯誤
        if ($validator->fails()) {
            return ErrorController::WrongDataType();
        }
        // 使用者不存在、帳密有誤
        $email = $request->email;
        $password = $request->password;
        $table = DB::table('user')->where('email', $email);
        if($table->doesntExist()){
            return ErrorController::InvalidLogin();
        }
        $user = $table->first();
        if(!Hash::check($password, $user->password)) {
            return ErrorController::InvalidLogin();
        }
        $access_token = hash("sha256", $email);
        $table->update(['access_token' => $access_token]);
        return response()->json([
            "success" => TRUE,
            "data" => [
                "id" => $user->id,
                "email" => $user->email,
                "nickname" => $user->nickname,
                "profile_image" => $user->profile_image,
                "type" => $user->type,
                "access_token" => $access_token,
            ]
        ]);
    }
    // 註冊
    public function register(Request $request) {
        if (!$request->has(['email', 'nickname', 'password', 'profile_image'])) {
            return ErrorController::MissingField();
        }
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'nickname' => 'required',
            'password' => 'required',
            'profile_image' => 'required|image'
        ]);
        // body或query資料格式錯誤
        if ($validator->fails()) {
            return ErrorController::WrongDataType();
        }
        // 密碼長度不正確
        if(strlen($request->password) < 4) {
            return ErrorController::PasswordNotSecure();
        }
        // 使用者已存在
        $table = DB::table("user")->where('email', $request->email);
        if($table->exists()) {
            return ErrorController::UserExists();
        }
        $profile_image = $request->profile_image->store('public', 'local');
        $profile_image = Storage::url($profile_image);
        $access_token = hash('sha256', $request->email);
        DB::table("user")->insert([
            'email' => $request->email,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
            'profile_image' => $profile_image,
            'type' => 'USER',
            'access_token' => $access_token,
        ]);
        $user = DB::table('user')->where('email', $request->email)->first();
        return response()->json([
            "success" => TRUE,
            "data" => [
                "id" => $user->id,
                "email" => $user->email,
                "nickname" => $user->nickname,
                "profile_image" => $user->profile_image,
                "type" => $user->type,
                "access_token" => $access_token,
            ]
        ]);
    }
    // 登出
    public function logout(Request $request) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        $table->update([
            'access_token' => ''
        ]);
        return response()->json([
            "success" => TRUE,
        ]);
    }
}
