<header class="header">
    <div class="header__logo">
        <img src="{{ asset('img/COACHTECH.png') }}" alt="COACHTECH">
    </div>

    <nav class="header__nav">
        <ul>
            <li><a href="/admin/attendance/list">勤怠一覧</a></li>
            <li><a href="/admin/staff/list">スタッフ一覧</a></li>
            <li><a href="/admin/stamp_correction_request/list">申請一覧</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
        </ul>
    </nav>
</header>
