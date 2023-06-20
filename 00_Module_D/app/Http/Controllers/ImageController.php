<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\ErrorController;

class ImageController extends Controller
{
    //上傳圖片
    public function upload(Request $request) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        $user = $table->first();
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        // 缺少必要欄位
        if (!$request->has(['image', 'title', 'description', 'width', 'height'])) {
            return ErrorController::MissingField();
        }
        // body或query資料格式錯誤
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'title' => 'required',
            'description' => 'required',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return ErrorController::WrongDataType();
        }
        // 圖片格式錯誤
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);
        if ($validator->fails()) {
            return ErrorController::InvalidFileFormat();
        }
        $image = $request->image->store('public', 'local');
        $image = Storage::url($image);
        $now = Carbon::now();
        $now = $now->toDateTimeString();
        $id = DB::table('image')->insertGetId([
            'user_id' => $user->id,
            'url' => $image,
            'title' => $request->title,
            'description' => $request->description,
            'width' => $request->width,
            'height' => $request->height,
            'view' => 0,
            'mimetype' => $request->image->getMimeType(),
            'updated_at' => $now,
            'created_at' => $now
        ]);
        $image = DB::table('image')->where('id', $id)->first();
        return response()-> json([
            "id" =>  $image->id, 
            "url" =>  $image->url, 
            "author" => [ 
                "id" =>  $user->id, 
                "email" =>  $user->email, 
                "nickname" =>  $user->nickname, 
                "profile_image" =>  $user->profile_image, 
                "type" =>  $user->type, 
            ], 
            "title" =>  $image->title, 
            "description" =>  $image->description, 
            "width" =>  $image->width, 
            "height" =>  $image->height, 
            "mimetype" =>  $image->mimetype, 
            "view_count" =>  $image->view, 
            "updated_at" =>  $image->updated_at, 
            "created_at" =>  $image->created_at
        ]);
    }
    // 更新圖片
    public function update(Request $request, $id) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        $user = $table->first();
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        // 不存在的圖片
        $table = DB::table('image')->where('id', $id)->where('deleted_at', NULL);
        if($table->doesntExist()) {
            return ErrorController::PostNotExists();
        }
        // 權限不足
        $image = $table->first();
        if($image->user_id != $user->id) {
            return ErrorController::PermissionDeny();
        }
        // 缺少必要欄位
        if (!$request->has(['title', 'description'])) {
            return ErrorController::MissingField();
        }
        // body或query資料格式錯誤
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return ErrorController::WrongDataType();
        }
        $table->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        return response()->json([
            "success" => TRUE
        ]);
    }
    // 取得圖片
    public function get($id) {
        $table= DB::table('image')->where('id', $id)->where('deleted_at', NULL);
        if($table->doesntExist()) {
            return ErrorController::PostNotExists();
        }
        $image = $table->first();
        $user = DB::table('user')->where('id', $image->user_id)->first();
        return response()-> json([
            "success" => TRUE, 
            "data"=> [ 
                "id" =>  $image->id, 
                "url" =>  $image->url, 
                "author" => [ 
                    "id" =>  $user->id, 
                    "email" =>  $user->email, 
                    "nickname" =>  $user->nickname, 
                    "profile_image" =>  $user->profile_image, 
                    "type" =>  $user->type, 
                ], 
                "title" =>  $image->title, 
                "description" =>  $image->description, 
                "width" =>  $image->width, 
                "height" =>  $image->height, 
                "mimetype" =>  $image->mimetype, 
                "view_count" =>  $image->view, 
                "updated_at" =>  $image->updated_at, 
                "created_at" =>  $image->created_at
            ]
        ]);
    }
    // 軟刪除圖片
    public function softdelete(Request $request, $id) {
        $token = $request->header('token');
        $table = DB::table('user')->where('access_token', $token);
        $user = $table->first();
        // 無效的Access token 
        if($table->doesntExist() || $token == '') {
            return ErrorController::InvalidAccessToken();
        }
        // 不存在的圖片
        $table = DB::table('image')->where('id', $id);
        if($table->doesntExist()) {
            return ErrorController::PostNotExists();
        }
        // 權限不足
        $image = $table->first();
        if($image->user_id != $user->id) {
            return ErrorController::PermissionDeny();
        }
        $now = Carbon::now()->toDateTimeString();
        DB::table('image')->where('id', $id)->update(['deleted_at' => $now]);
        return response()->json([
            "success" => TRUE
        ]);
    }
    // 搜尋圖片
    public function searchImages(Request $request) {
        // 這題看不出來需要甚麼鬼缺少必要欄位
        $order_by = $request->input('order_by', 'created_at');
        $order_type = $request->input('order_type', 'asc');
        $keyword = $request->input('keyword');
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);
        if($order_by != 'created_at' && $order_by != 'updated_at') {
            return ErrorController::WrongDataType();
        }
        if($order_type != 'asc' && $order_type != 'desc') {
            return ErrorController::WrongDataType();
        }
        $table = DB::table('image')->orderBy($order_by, $order_type);
        if($keyword != "") {
            $table = $table->where('title', 'LIKE', '%'.$keyword.'%')->orWhere('description', 'LIKE', '%'.$keyword.'%');
        }
        $images = $table->get();
        $response = collect([]);;
        foreach($images as $image) {
            $user = DB::table('user')->where('id', $image->user_id)->first();
            $object = collect([
                "id" =>  $image->id, 
                "url" =>  $image->url, 
                "author" => [ 
                    "id" =>  $user->id, 
                    "email" =>  $user->email, 
                    "nickname" =>  $user->nickname, 
                    "profile_image" =>  $user->profile_image, 
                    "type" =>  $user->type, 
                ], 
                "title" =>  $image->title, 
                "description" =>  $image->description, 
                "width" =>  $image->width, 
                "height" =>  $image->height, 
                "mimetype" =>  $image->mimetype, 
                "view_count" =>  $image->view, 
                "updated_at" =>  $image->updated_at, 
                "created_at" =>  $image->created_at
            ]);
            $response->push($object);
        }
        $response = $response->chunk($page_size);
        return response()->json([
            "success" => TRUE, 
            "data"=> $response->take($page),
        ]);
    }

    //  瀏覽使用者的圖片 
    public function getUserImages($id) {
        // 不存在的使用者
        $table = DB::table('user')->where('id', $id);
        if($table->doesntExist()) {
            return ErrorController::UserNotExists();
        }
        $user = $table->first();
        $images = DB::table('image')->where('user_id', $id)->where('deleted_at', NULL)->get();
        $response = [];
        foreach($images as $image) {
            $object = [
                "id" =>  $image->id, 
                "url" =>  $image->url,
                "title" =>  $image->title, 
                "description" =>  $image->description, 
                "width" =>  $image->width, 
                "height" =>  $image->height, 
                "mimetype" =>  $image->mimetype, 
                "view_count" =>  $image->view, 
                "updated_at" =>  $image->updated_at, 
                "created_at" =>  $image->created_at
            ];
            array_push($response, $object);  
        }
        return response()->json([
            "success" => TRUE, 
            "data"=> [
                "author" => [ 
                    "id" =>  $user->id, 
                    "email" =>  $user->email, 
                    "nickname" =>  $user->nickname, 
                    "profile_image" =>  $user->profile_image, 
                    "type" =>  $user->type, 
                ], 
                'images' => $response
            ]
        ]);
    }
    // 取的熱門圖片
    public function getPopular() {
        $images = DB::table('image')->orderBy('view', 'asc')->take(10)->get();
        $response = [];
        foreach($images as $image) {
            $user = DB::table('user')->where('id', $image->user_id)->first();
            $object = [
                "id" =>  $image->id, 
                "url" =>  $image->url, 
                "author" => [ 
                    "id" =>  $user->id, 
                    "email" =>  $user->email, 
                    "nickname" =>  $user->nickname, 
                    "profile_image" =>  $user->profile_image, 
                    "type" =>  $user->type, 
                ], 
                "title" =>  $image->title, 
                "description" =>  $image->description, 
                "width" =>  $image->width, 
                "height" =>  $image->height, 
                "mimetype" =>  $image->mimetype, 
                "view_count" =>  $image->view, 
                "updated_at" =>  $image->updated_at, 
                "created_at" =>  $image->created_at
            ];
            array_push($response, $object);  
        }
        return response()->json([
            "success" => TRUE, 
            "data"=> $response
        ]);
    }
}
