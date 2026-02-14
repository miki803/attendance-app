@extends('layouts.app')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

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
    <a href="/admin/login" class="link">管理者ログインはこちら</a>
</form>
</div>
@endsection
