
<header class="header">
    <div class="header__logo">
        <a href="/"><img src="{{ asset('img/COACHTECH.png') }}" alt="ロゴ"></a>
    </div>

    <nav class="header__nav">
        <ul>
            <li><a href="/attendance">勤怠</a></li>
            <li><a href="/attendance/list">勤怠一覧</a></li>
            <li><a href="/correction/user_list">申請</a></li>
            <li>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            
        </ul>
    </nav>
</header>