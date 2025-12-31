勤怠一覧画面（一般ユーザー）
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
