@extends('layouts.app')

@section('title','申請一覧画面（一般ユーザー）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/list.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
