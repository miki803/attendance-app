@extends('layouts.app')

@section('title','勤怠詳細画面（一般ユーザー）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/detail.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="page">
    <div class="card">
        <h1 class="card__title">勤怠詳細</h1>
        <form method ="POST" action="{{ route('stamp_correction_request.store') }}">

            @csrf
            @if($attendance)
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <input type="hidden" name="date" value="{{ $attendance->date->toDateString() }}">
            @else
                <input type="hidden" name="date" value="{{ $date }}">
            @endif
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td>
                        {{ $attendance?->user?->name ?? auth()->user()->name }}
                    </td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>
                        @if($attendance)
                            {{ $attendance->date->format('Y年')}}
                            {{ $attendance->date->format('n月j日')}}
                        @else
                            {{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time" value="{{ old('start_time', $attendance?->start_time) }}" {{ $isPending ? 'readonly' : '' }}>
                        ~
                        <input type="time" name="end_time" value="{{ old('end_time', $attendance?->end_time) }}" {{ $isPending ? 'readonly' : '' }}>
                    </td>
                </tr>

                @foreach ($breakTimes as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td>
                        <input type="time" name="requested_breaks[{{ $i }}][start]" value="{{ old("requested_breaks.$i.start", substr($break->start_time, 0, 5)) }}"{{ $isPending ? 'readonly' : '' }}>
                        ~
                        <input type="time" name="requested_breaks[{{ $i }}][end]" value="{{ old("requested_breaks.$i.end", substr($break->end_time, 0, 5)) }}"{{ $isPending ? 'readonly' : '' }}>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>休憩{{ count($breakTimes) + 1 }}</th>
                    <td>
                        <input type="time" name="requested_breaks[{{ count($breakTimes) }}][start]" {{ $isPending ? 'readonly' : '' }}>
                        ~
                        <input type="time" name="requested_breaks[{{ count($breakTimes) }}][end]" {{ $isPending ? 'readonly' : '' }}>
                    </td>
                </tr>


                <tr>
                    <th>備考</th>
                    <td>
                        <input class="remark-input" type="text" name="remark" value="{{ old('remark', $attendance?->remark) }}" {{ $isPending ? 'readonly' : '' }}>
                    </td>
                </tr>
            </table>
            <div class="button-area">
            @if ($errors->any())
                <div style="color:red;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (! $isPending)
                <button class="btn btn--black" type="submit">修正</button>
            @endif
            @if ($isPending)
                <div class="pending-message">
                    承認待ちのため修正はできません。
                </div>
            @endif

            </div>
        </form>
    </div>
</div>

@endsection
