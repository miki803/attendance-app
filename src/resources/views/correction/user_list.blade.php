@extends('layouts.app')

@section('title','申請一覧画面（一般ユーザー）')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/correction.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')

<div class="page">
    <div class="card">

        <h2 class="card__title">申請一覧</h2>

        <div class="border">
            <ul class="border__list">
                <li class="{{ $status === 'pending' ? 'active' : '' }}">
                    <a href="{{ url('/stamp_correction_request/list?status=pending') }}">
                        承認待ち
                    </a>
                </li>
                <li class="{{ $status === 'approved' ? 'active' : '' }}">
                    <a href="{{ url('/stamp_correction_request/list?status=approved') }}">
                        承認済み
                    </a>
                </li>
            </ul>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ optional($request->attendance)->date?->format('Y/m/d') }}</td>
                    <td>{{ $request->remark ?? '—' }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ url('/attendance/detail/' . $request->attendance_id) }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">申請はありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection