@extends('layouts.app')

@section('title','ログイン')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="attendance">
<form class="authenticate center" action="{{ route('login') }}" method="post" >
    @csrf
    <h1 class="page__title">ログイン</h1>
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
    <button class="btn btn--big">ログインする</button>
    <a href="/register" class="link">会員登録はこちら</a>
</form>
</div>
@endsection
