<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StampCorrectionRequestController;



Route::get('/', function () {
    return view('welcome');
});

//一般ユーザー
Route::middleware('auth')->group(function () {
    // 出勤登録画面（一般ユーザー）
    Route::get('/attendance',[AttendanceController::class,'index']);
    // 出勤
    Route::post('/attendance/start',[AttendanceController::class,'start']);
    // 休憩
    Route::post('/attendance/break',[AttendanceController::class,'break']);
    // 退勤
    Route::post('/attendance/end',[AttendanceController::class,'end']);
    // 勤怠一覧画面（一般ユーザー）
    Route::get('/attendance/list',[AttendanceController::class,'list']);
    // 勤怠詳細画面（一般ユーザー）
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'detail']);

    //申請一覧画面（一般ユーザー）
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'userList']);
    //修正申請送信
    Route::post('/stamp_correction_request',[StampCorrectionRequestController::class,'store']);
    //申請詳細
    Route::get('/stamp_correction_request/{id}',[StampCorrectionRequestController::class,'show']);
});

//管理者
Route::prefix('admin')->middleware('auth')->group(function () {
    //勤怠一覧画面（管理者）
    Route::get('/attendance/list',[AdminAttendanceController::class,'index']);
    //勤怠詳細画面（管理者）
    Route::get('/attendance/{id}',[AdminAttendanceController::class,'detail']);
    //スタッフ別勤怠一覧画面（管理者）
    Route::get('/attendance/staff/{id}',[AdminAttendanceController::class,'staff']);
    //勤怠修正
    Route::post('/attendance/update',[AdminAttendanceController::class,'update']);

    //スタッフ一覧画面（管理者）
    Route::get('/staff/list',[StaffController::class,'index']);

    //申請一覧画面（管理者）
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'adminList']);
    //修正申請承認画面（管理者）
    Route::get('/stamp_correction_request/approve/{id}',[StampCorrectionRequestController::class,'show']);
    //承認
    Route::post('/stamp_correction_request/approve/{id}',[StampCorrectionRequestController::class,'approve']);

    });
