@extends('layouts.app')

@section('title','勤怠一覧画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/list.css') }}">
@endsection

@section('content')

@include('components.header_admin')

<div class="page">
    <div class="card">
        <h2 class="card__title">{{ $currentDate->format('Y年m月d日') }}の勤怠</h2>

        <div class="month-nav">
            <a href="?date={{ $currentDate->copy()->subDay()->format('Y-m-d') }}"><-前日</a>
            <span>{{ $currentDate->format('Y/m/d') }}</span>
            <a href="?date={{ $currentDate->copy()->addDay()->format('Y-m-d') }}">翌日-></a>
        </div>
        <div class="table-card">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    @php
                        $attendance = $user->attendances->first();
                    @endphp
                    <tr>
                        <td>{{ $user->name }}</td> <!-- 名前 -->
                        <td>{{ $attendance?->start_time ?? '-' }}</td> <!-- 出勤 -->
                        <td>{{ $attendance?->end_time ?? '-' }}</td> <!-- 退勤 -->
                        <td>{{ $attendance->break_time ?? '-' }}</td> <!-- 休憩 -->
                        <td>{{ $attendance->working_time ?? '-' }}</td> <!-- 合計 -->
                        <td>
                            @if ($attendance)
                                <a href="{{ url('/admin/attendance/' . $attendance->id) }}">詳細</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
