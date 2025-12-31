勤怠詳細画面（一般ユーザー）
<h1>勤怠詳細</h1>

<p>ID: {{ $attendance['id'] }}</p>
<p>日付: {{ $attendance['date'] }}</p>
<p>出勤: {{ $attendance['start_time'] }}</p>
<p>退勤: {{ $attendance['end_time'] ?? '未退勤' }}</p>
<p>状態: {{ $attendance['status'] }}</p>

<h2>休憩</h2>
<ul>
@foreach ($breakTimes as $b)
    <li>{{ $b['start_time'] }} 〜 {{ $b['end_time'] }}</li>
@endforeach
</ul>

<p><a href="/attendance/list">一覧に戻る</a></p>
