@extends('layouts.app')

@section('title','勤怠詳細画面（管理者）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/detail.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header_admin')

<div class="page">
    <div class="card">
        <h1 class="card__title">勤怠詳細</h1>
        <form method ="POST" action="{{ route('admin.attendance.update') }}">
            @csrf
            @if($attendance)
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <input type="hidden" name="user_id" value="{{ $attendance->user_id ?? $user->id ?? auth()->id() }}">
                <input type="hidden" name="date" value="{{ $attendance->date->toDateString() }}">
            @else
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
            @endif
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td>
                        {{ $attendance?->user?->name ?? $user->name }}
                    </td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>
                    @if($attendance)
                        {{ $attendance->date->format('Y年')}}
                    @else
                        {{ \Carbon\Carbon::parse($date)->format('n月j日')}}
                    @endif
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time" value="{{ old('start_time', $attendance?->start_time ? substr($attendance->start_time,0,5) : '') }}"{{ $isPending ? 'disabled' : '' }}>
                        ~
                        <input type="time" name="end_time" value="{{ old('end_time', $attendance?->end_time ? substr($attendance->end_time,0,5) : '') }}"{{ $isPending ? 'disabled' : '' }}>
                    </td>
                </tr>

                @foreach ($breakTimes as $i=> $break)
                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="time" name="breaks[{{ $i }}][start]" value="{{ $break->start_time ? substr($break->start_time,0,5) : '' }}">
                        ~
                        <input type="time" name="breaks[{{ $i }}][end]" value="{{ $break->end_time ? substr($break->end_time,0,5) : '' }}">

                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>休憩{{ count($breakTimes) + 1 }}</th>
                    <td>
                        <input type="time" name="breaks[{{ count($breakTimes) }}][start]" {{ $isPending ? 'disabled' : '' }}>
                        ~
                        <input type="time" name="breaks[{{ count($breakTimes) }}][end]" {{ $isPending ? 'disabled' : '' }}>
                    </td>
                </tr>


                <tr>
                    <th>備考</th>
                    <td>
                        <input class="remark-input" type="text" name="remark" value="{{old('remark')}}" {{ $isPending ? 'disabled' : '' }}>
                    </td>
                </tr>
            </table>
            <div class="button-area">
            @if (! $isPending)
                <button class="btn btn--black" type="submit">修正
                </button>
            @endif

            </div>
        </form>
    </div>
</div>

@endsection
