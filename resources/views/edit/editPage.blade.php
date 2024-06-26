<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メニュー編集</title>
    <link rel="stylesheet" href="{{ url('css/editPage.css') }}">
</head>

<body>
    <h1>メニュー編集画面</h1>

    <div class="changePage">
        <a href="{{ route('edit.postPage') }}" id="postPage">メニュー掲載画面</a>
    </div>
    <div class="changePage">
        <a href="{{ route('edit.create') }}" id="createPage">新規メニュー追加画面</a>
    </div>

    <hr>
    <h3>本日のメニュー</h3>
    <div class="menuViewer">
        @forelse ($MenuViewer as $menu)
            @if ($menu->show_date == date('Y-m-d'))
                <div class="menu" data-id="{{ $menu->id }}">
                    <img src="{{ asset($menu->menuList->picture) }}" alt="{{ $menu->menuList->menu }}">
                    <p>{{ $menu->menuList->menu ? $menu->menuList->menu : '-' }}</p>
                    <p><span>&#xa5</span>{{ $menu->menuList->price ? $menu->menuList->price : '-' }}</p>

                    <div class="controller">
                        <div>
                            <input type="checkbox" data-id="{{ $menu->id }}" class="toggle"
                                {{ $menu->sold_out ? 'checked' : '' }}>完売</input>
                        </div>
                        <button data-id="{{ $menu->id }}" class="delete">削除</button>
                    </div>
                </div>
            @endif
        @empty
            <p>No menus yet</p>
        @endforelse
    </div>

    <hr>
    <h3>営業カレンダーを更新</h3>
    <div class="submitCalendar">
        <form action="{{ route('edit.calendar') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <img id="previewCalendar">
                <input type="file" name="calendar" onchange="previewFile(this);">
            </div>
            @error('picture')
                <div>{{ $message }}</div>
            @enderror

            <button type="submit" name="submit">更新</button>
        </form>
    </div>

    <hr>
    <div class="accountPage">
        <a href="{{ route('profile.edit') }}" >
            {{ __('アカウント編集') }}
        </a>
    </div>

    <div class="logoutForm">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                        this.closest('form').submit();">
                {{ __('ログアウト') }}
            </a>
        </form>
    </div>

    <script>
        function previewFile(event) {
            let fileData = new FileReader();
            fileData.onload = (function() {
                const preview = document.getElementById('previewCalendar');
                preview.src = fileData.result;
                preview.style.width = "200px";
                preview.style.height = "150px";

            });
            fileData.readAsDataURL(event.files[0]);
        }

        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                fetch(`/editPage/${checkbox.dataset.id}/toggle`, {
                    method: 'POST',
                    // method: 'PATCH',
                    body: new URLSearchParams({
                        id: checkbox.dataset.id,
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });
                // checkbox.parentNode.parentNode.parentNode.classList.toggle('soldOut');
            });
        });

        const deletes = document.querySelectorAll('.delete');
        deletes.forEach(button => {
            button.addEventListener('click', () => {
                fetch(`/editPage/${button.parentNode.parentNode.dataset.id}/destroy`, {
                    method: 'POST',
                    // method: 'DELETE',
                    body: new URLSearchParams({
                        id: button.dataset.id,
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });

                button.parentNode.parentNode.remove();
            });
        });
    </script>
</body>



</html>
