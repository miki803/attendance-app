出勤登録画面（一般ユーザー）
@extends('layouts.app')

@if(!$attendance)
{{--未出勤--}}
    <form method="POST" action="/attendance/start">
        @csrf
        <button>出勤</button>
    </form>

@elseif ($attendance->status === 'working')
{{--出勤中--}}
    <form method="POST" action="/attendance/break/start">
        @csrf
        <button>休憩開始</button>
    </form>
    <form method="POST" action="/attendance/break/end">
        @csrf
        <button>休憩終了</button>
    </form>
    <form method="POST" action="/attendance/end">
        @csrf
        <button>退勤</button>
    </form>

@else 
{{--退勤済み--}}
    <p>お疲れ様でした。</p>
    <a href="/attendance/list">勤怠一覧を見る</a>

@endif