@extends('layouts.app')

@section('title','スタッフ別勤怠一覧画面（管理者））')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/attendance.css') }}">
@endsection

@section('content')

@include('components.header_admin')

<div class="page">
    <div class="card">

        <h2 class="card__title">{{ $staff->name }}さんの勤怠</h2>

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
                        </td> 
                        <td>{{ $attendance?->start_time_formatted ?? '-' }} </td> 
                        <td>{{ $attendance?->end_time_formatted ?? '-' }}</td> 
                        <td>{{ $attendance?->break_time ?? '-' }}</td> 
                        <td>{{ $attendance?->working_time ?? '-' }}</td> 
                        <td>
                            @if($attendance)
                                <a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
                            @else
                                <a href="{{ route('admin.attendance.detail.date', ['user' => $staff->id,'date' => $date->format('Y-m-d') ]) }}">
                                    詳細
                                </a>
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