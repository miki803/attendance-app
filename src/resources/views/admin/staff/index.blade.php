@extends('layouts.app')

@section('title','スタッフ一覧画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/staff.css')  }}">
@endsection

@section('content')

@include('components.header_admin')

<div class="page">
    <div class="card">

        <h2 class="card__title">スタッフ一覧</h2>


        <div class="table-card">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>月次勤怠</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td> 
                        <td>{{ $staff->email}}</td> 
                        <td>
                            <a href="{{ route('admin.attendance.staff',$staff->id) }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection