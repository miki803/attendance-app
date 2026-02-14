@extends('layouts.app')

@section('title','出勤登録画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/index.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class ="attendance">
    <div class="attendance__status">
        @if(!$attendance)
            勤務外
        @elseif($attendance->status === 'working' && $onBreak)
            休憩中
        @elseif($attendance->status === 'working')
            出勤中
        @else
            退勤済
        @endif
            
    </div>
    <div class ="attendance__date">
        {{ now()->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}
    </div>
    <div class="attendance__time" id="current-time">
        --:--
    </div>
    
    <script src="{{ asset('js/attendance_clock.js') }}"></script>

    <div class="attendance__actions">
        <!-- 出勤前 -->
        @if(!$attendance)
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button class="btn btn--primary">出勤</button>
            </form>
        <!--出勤中-->
        @elseif($attendance->status === 'working' && !$onBreak)
            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button class="btn btn--primary">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.break.start') }}">
                @csrf
                <button class="btn">休憩入</button>
            </form>

        <!--休憩中-->
        @elseif($attendance->status === 'working' && $onBreak)
            <form method="POST" action="{{ route('attendance.break.end') }}">
                @csrf
                <button class="btn">休憩戻</button>
            </form>
        <!--退勤済み-->
        @else
            <p>お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection

