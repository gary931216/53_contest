<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\ErrorController;

class CommentController extends Controller
{
    //取的圖片評論
    public function get($id) {
        // 不存在的圖片
        $table = DB::table('image')->where('id', $id)->where('deleted_at', NULL);
        if($table->doesntExist()) {
            return ErrorController::PostNotExists();
        }
        $comments = DB::table('comment')->where('image_id', $id)->get();
        $response = [];
        foreach($comments as $comment) {
            $user = DB::table('user')->where('id', $comment->user_id)->first();
            $object = [
                "id" => $comment->id,
                "user" => [ 
                    "id" =>  $user->id, 
                    "email" =>  $user->email, 
                    "nickname" =>  $user->nickname, 
                    "profile_image" =>  $user->profile_image, 
                    "type" =>  $user->type, 
                ], 
                "text" => $comment->text,
            ];
            array_push($response, $object);
        }
        return response()->json([
            "success" => TRUE, 
            "data"=> $response
        ]);
    }
    //發布圖片評論
    public function post(Request $request, $id) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        $user = $table->first();
        // 不存在的圖片
        $table = DB::table('image')->where('id', $id)->where('deleted_at', NULL);
        if($table->doesntExist()) {
            return ErrorController::PostNotExists();
        }
        // 缺少必要欄位
        if (!$request->has(['text'])) {
            return ErrorController::MissingField();
        }
        // body或query資料格式錯誤
        $validator = Validator::make($request->all(), [
            'text' => 'required',
        ]);
        if ($validator->fails()) {
            return ErrorController::WrongDataType();
        }
        DB::table('comment')->insert([
            'image_id' => $id,
            'user_id' => $user->id,
            'text' => $request->text,
        ]);
        return response()->json([
            "success" => TRUE
        ]);
    }
    //刪除圖片評論
    public function delete(Request $request, $id) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        $user = $table->first();
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        // 不存在的評論
        $table = DB::table('comment')->where('id', $id);
        if($table->doesntExist()) {
            return ErrorController::CommentNotExists();
        }
        // 權限不足
        $comment = $table->first();
        if($comment->user_id != $user->id) {
            return ErrorController::PermissionDeny();
        }
        DB::table('comment')->where('id', $id)->delete();
        return response()->json([
            "success" => TRUE
        ]);
    }
    public function getTime() {
        $now = Carbon::now()->format("Y-m-d\TH:m:s");
        return response()->json([
            'timer' => $now
        ]);
    }
}
