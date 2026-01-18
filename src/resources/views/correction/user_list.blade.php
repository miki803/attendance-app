@extends('layouts.app')

@section('title','申請一覧画面（一般ユーザー）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/list.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')

<div class="border">
    <ul class="border__list">
        <li><a href="{{ route('items.list', ['tab'=>'recommend', 'search'=>$search]) }}">承認待ち</a></li>
        @if(!auth()->guest())
        <li><a href="{{ route('items.list', ['tab'=>'mylist', 'search'=>$search]) }}">承認済み</a></li>
        @endif
    </ul>
</div>

@endsection