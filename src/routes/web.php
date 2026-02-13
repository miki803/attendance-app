<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StampCorrectionRequestController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

//一般ユーザー
Route::middleware('auth')->group(function () {
    // 出勤登録画面（一般ユーザー）
    Route::get('/attendance',[AttendanceController::class,'index']);
    // 出勤
    Route::post('/attendance/start',[AttendanceController::class,'start'])->name('attendance.start');
    // 休憩開始
    Route::post('/attendance/break/start',[AttendanceController::class,'breakStart'])->name('attendance.break.start');
    // 休憩終了
    Route::post('/attendance/break/end',[AttendanceController::class,'breakEnd'])->name('attendance.break.end');
    // 退勤
    Route::post('/attendance/end',[AttendanceController::class,'end'])->name('attendance.end');
    // 勤怠一覧画面（一般ユーザー）
    Route::get('/attendance/list',[AttendanceController::class,'list'])
    ->name('attendance.list');
    // 勤怠詳細画面（一般ユーザー）
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'detail'])
    ->name('attendance.detail');
    Route::get('/attendance/detail/date/{date}',[AttendanceController::class,'detailByDate'])
    ->name('attendance.detail.date');

    //申請一覧画面（一般ユーザー）
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'userList'])
    ->name('correction.user_list');
    //修正申請送信
    Route::post('/stamp_correction_request',[StampCorrectionRequestController::class,'store'])
    ->name('stamp_correction_request.store');
    //申請詳細
    Route::get('/stamp_correction_request/{id}',[StampCorrectionRequestController::class,'userShow']);
});

//管理者
Route::prefix('admin')->middleware('auth', 'is_admin')->group(function () {
    //勤怠一覧画面（管理者）
    Route::get('/attendance/list',[AdminAttendanceController::class,'index'])
    ->name('admin.attendance');

    //スタッフ一覧画面（管理者）
    Route::get('/staff/list',[StaffController::class,'index'])
    ->name('admin.staff.list');
    //スタッフ別勤怠一覧画面（管理者）
    Route::get('/attendance/staff/{user}',[AdminAttendanceController::class,'staff'])
    ->name('admin.attendance.staff');
    //勤怠詳細画面（管理者）
    Route::get('/attendance/{id}',[AdminAttendanceController::class,'detail']
    )->name('admin.attendance.detail');

    Route::get('/attendance/detail/date/{user}/{date}',[AdminAttendanceController::class, 'detailByDate']
    )->name('admin.attendance.detail.date');
    //勤怠修正
    Route::post('/attendance/update',[AdminAttendanceController::class,'update']
    )->name('admin.attendance.update');

    //申請一覧画面（管理者）
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'adminList']
    )->name('admin.correction.list');
    //修正申請承認画面（管理者）
    Route::get('/stamp_correction_request/approve/{id}',[StampCorrectionRequestController::class,'adminShow'])->name('admin.correction.show');
    //承認
    Route::post('/stamp_correction_request/approve/{id}',[StampCorrectionRequestController::class,'approve'])->name('admin.correction.approve');

    });

