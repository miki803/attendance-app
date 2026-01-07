<!--ログイン画面（管理者）-->
@extends('layouts.app')

@section('title','管理者ログイン')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<form class="authenticate center" action="/login" method="post" >
    @csrf
    <h1 class="page__title">管理者ログイン</h1>
    <label class="entry__name" for="mail" >メールアドレス</label>
    <input class="input" name="email" id="mail" type="email"  value="{{ old('email') }}">
    <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <label class="entry__name" for="password" >パスワード</label>
    <input class="input" name="password" id="password" type="password" >
    <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="btn btn--big">管理者ログインする</button>

</form>
@endsection
