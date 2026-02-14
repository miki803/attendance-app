@extends('layouts.app')

@section('title','会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

@section('content')

@include('components.header')
<form class="authenticate center" action="/register" method="post" >
    @csrf
    <h1 class="page__title">会員登録</h1>
    <label class="entry__name" for="name" >ユーザ名</label>
    <input class="input" name="name" id="name" type="text"  value="{{ old('name') }}">
    <div class="form__error">
        @error('name')
        {{ $message }}
        @enderror
    </div>
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
    <label class="entry__name" for="password_confirm" >確認用パスワード</label>
    <input class="input" name="password_confirmation" id="password_confirm" type="password" >
    <button class="btn btn--big">登録する</button>
    <a href="/login" class="link">ログインはこちら</a>
</form>
@endsection

