@extends('layouts.app')

@section('title','勤怠一覧画面（一般ユーザー')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')

）
<h1>勤怠一覧</h1>

<ul>
@foreach ($attendances as $a)
    <li>
        {{ $a['date'] }}
        {{ $a['start_time'] }} 〜 {{ $a['end_time'] ?? '未退勤' }}
        ({{ $a['status'] }})

        <a href="/attendance/detail/{{ $a['id'] }}">詳細</a>
    </li>
@endforeach
</ul>
