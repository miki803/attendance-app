@extends('layouts.app')

@section('title','勤怠一覧画面（一般ユーザー）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/list.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')

<div class="page">
    <div class="card">

        <h2 class="card__title">勤怠一覧</h2>

        <div class="month-nav">
            <a href="?month={{ $currentMonth->copy()->subMonth()->format('Y-m') }}"><- 前月</a>
            <span>{{ $currentMonth->format('Y/m') }}</span>
            <a href="?month={{ $currentMonth->copy()->addMonth()->format('Y-m') }}">翌月 -></a>
        </div>

        <div class="table-card">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($dates as $date)
                @php
                    $attendance = $attendances[$date->format('Y-m-d')] ?? null;
                @endphp
                    <tr>
                        <td>
                            {{ $date->format('m/d') }}
                            ({{ $date->isoFormat('dd') }})
                        </td> <!-- 日付 -->
                        <td>{{ $attendance?->start_time_formatted ??' ' }} </td> <!-- 出勤 -->
                        <td>{{ $attendance?->end_time_formatted ??'' }}</td> <!-- 退勤 -->
                        <td>{{ $attendance?->break_time ?? '' }}</td> <!-- 休憩 -->
                        <td>{{ $attendance?->working_time ?? '' }}</td> <!-- 合計 -->
                        <td>
                                <a href="{{ route('attendance.detail.date',$date->format('Y-m-d')) }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection



