<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ember Daily — a little focus for your café day. Plan tasks, follow your progress, and make room for good things.">
    <title>@yield('title', 'Today') · Ember Daily</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('cafe-base.css') }}">
    <link rel="stylesheet" href="{{ asset('daily.css') }}">
    <script src="{{ asset('daily.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<aside class="sidebar">
    <a class="brand" href="{{ route('today') }}" aria-label="Ember Daily home"><span class="brand-mark" aria-hidden="true">e<span>•</span></span><span>ember<span class="brand-sub">DAILY WORKSPACE</span></span></a>
    <p class="nav-label">YOUR DAY, THOUGHTFULLY PLANNED</p>
    <nav aria-label="Main navigation">
        @foreach(['today' => ['sun', 'Today', request()->routeIs('today')], 'tasks.index' => ['list', 'Task list', request()->routeIs('tasks.*')], 'profile' => ['user', 'Profile', request()->routeIs('profile')], 'about' => ['info', 'About', request()->routeIs('about')]] as $route => [$icon, $label, $active])
        <a href="{{ route($route) }}" class="nav-item {{ $active ? 'active' : '' }}" @if($active) aria-current="page" @endif><span class="line-icon icon-{{ $icon }}" aria-hidden="true"></span><span>{{ $label }}</span><span class="nav-arrow" aria-hidden="true">↗</span></a>
        @endforeach
    </nav>
    <div class="sidebar-bottom"><div class="sidebar-ritual"><span class="daily-star" aria-hidden="true">✳</span><strong>One thing.<br>Then the next.</strong><p>There’s good in the everyday.</p></div><a class="workspace" href="{{ route('profile') }}"><span class="avatar">KC</span><span><b>{{ config('developer.username') }}</b><small>My daily workspace</small></span><span class="status-dot" aria-hidden="true"></span></a></div>
</aside>
<div class="shell">
    <header class="topbar"><span>THE EMBER WORKSPACE <span class="topbar-divider">/</span><b>@yield('title', 'Today')</b></span><div><time datetime="{{ today()->toDateString() }}">{{ today()->format('l, d F Y') }}</time>@auth<span>{{ auth()->user()->username }}</span><form method="post" action="{{ route('logout') }}" style="display:inline">@csrf<button class="text-link" type="submit" style="border:0;background:none;cursor:pointer">Log out</button></form>@else<a class="text-link" href="{{ route('login') }}">Log in</a><a class="text-link" href="{{ route('register') }}">Register</a>@endauth</div></header>
    <main id="main" class="main daily-main">
        @if(session('success'))<div class="flash-message" role="status"><span aria-hidden="true">✓</span>{{ session('success') }}</div>@endif
        @yield('content')
    </main>
    <footer class="footer"><span>© {{ now()->year }} Ember Daily</span><span>A little focus. A well-brewed day.</span></footer>
</div>
<dialog id="delete-dialog" class="confirm-dialog" aria-labelledby="delete-title"><div><span class="eyebrow">MAKE A LITTLE ROOM</span><h2 id="delete-title">Remove this task?</h2><p>This will permanently remove <strong id="delete-task-name"></strong> from your list.</p><div class="dialog-actions"><button type="button" class="button light" id="cancel-delete">Keep task</button><button type="button" class="button danger" id="confirm-delete">Remove task</button></div></div></dialog>
</body>
</html>
