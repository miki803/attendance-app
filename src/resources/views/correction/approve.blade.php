@extends('layouts.app')

@section('title','修正申請承認画面（管理者））')

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

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>
                    {{ $correction->user?->name ?? '—' }}
                </td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    {{ $correction->attendance->date->format('Y年') }}
                    {{ $correction->attendance->date->format('n月j日') }}
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ \Carbon\Carbon::parse($attendanceDetail?->start_time)->format('H:i') }}
                    ~
                    {{ \Carbon\Carbon::parse($attendanceDetail?->end_time)->format('H:i') }}
                </td>
            </tr>

            @foreach ($breakDetails as $i => $break)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td>
                    {{ \Carbon\Carbon::parse($break->start_time)->format('H:i')  }}
                    ~
                    {{ \Carbon\Carbon::parse($break->end_time)->format('H:i')  }}
                </td>
            </tr>
            @endforeach

            <tr>
                <th>備考</th>
                <td>
                    {{ $attendanceDetail?->note }}
                </td>
            </tr>
        </table>

        <div class="button-area">
            @if ($correction->status === 'pending')
                <form method="POST" action="{{ url('/admin/stamp_correction_request/approve/' . $correction->id) }}">
                    @csrf
                    <button class="btn btn--black" type="submit">承認</button>
                </form>
            @else
                <div class="approved-message">承認済み</div>
            @endif
        </div>
    </div>
</div>

@endsection
