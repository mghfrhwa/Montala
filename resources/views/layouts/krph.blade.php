<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Input Data') — SITALANG</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/sitalang.css') }}">
@stack('styles')
</head>
<body>
<section class="view active">
  <div class="shell">
    <aside class="sidebar">
      <div class="brand-mark"><div class="stake"></div><span>SITALANG</span></div>
      <div class="nav-group">
        <p class="nav-label">Kerja lapangan</p>
        <a href="{{ route('krph.progres') }}" class="nav-item {{ request()->routeIs('krph.progres') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Progres wilayah saya
        </a>
        <a href="{{ route('krph.input') }}" class="nav-item {{ request()->routeIs('krph.input') || request()->routeIs('krph.input.store') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
          Input data lapangan
        </a>
        <a href="{{ route('krph.riwayat') }}" class="nav-item {{ request()->routeIs('krph.riwayat') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 109-9M3 12h6M3 12l3-3M3 12l3 3"/></svg>
          Riwayat input
        </a>
      </div>
      <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <div class="user-chip" style="cursor:pointer;" onclick="this.closest('form').submit()">
            <div class="av">{{ strtoupper(substr(auth()->user()->nama ?? 'KR', 0, 2)) }}</div>
            <div class="info"><p>{{ auth()->user()->nama }}</p><span>{{ optional(auth()->user()->rph)->nama_rph }} · {{ optional(optional(auth()->user()->rph)->bkph)->nama_bkph }} · klik untuk keluar</span></div>
          </div>
        </form>
      </div>
    </aside>

    <main class="main">
      @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
      @endif
      @yield('content')
    </main>
  </div>
</section>
@stack('scripts')
</body>
</html>
