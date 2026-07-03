<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard') — SITALANG</title>
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
        <p class="nav-label">Monitoring</p>
        <a href="{{ route('kph.ringkasan') }}" class="nav-item {{ request()->routeIs('kph.ringkasan') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Ringkasan &amp; progres per petak
        </a>
        <a href="{{ route('kph.peta') }}" class="nav-item {{ request()->routeIs('kph.peta') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 20l-6-3V4l6 3 6-3 6 3v13l-6-3-6 3z"/></svg>
          Peta sebaran
        </a>
      </div>
      <div class="nav-group">
        <p class="nav-label">Perencanaan</p>
        <a href="{{ route('kph.target') }}" class="nav-item {{ request()->routeIs('kph.target') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8"/></svg>
          Target progres
        </a>
        <a href="{{ route('kph.export') }}" class="nav-item {{ request()->routeIs('kph.export') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
          Export laporan
        </a>
      </div>
      <div class="nav-group">
        <p class="nav-label">Administrasi</p>
        <a href="{{ route('kph.user') }}" class="nav-item {{ request()->routeIs('kph.user') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3.2"/><path d="M2.5 20a6.5 6.5 0 0113 0"/><circle cx="17.5" cy="8.5" r="2.4"/><path d="M15.5 12.2a5.2 5.2 0 016 4.9"/></svg>
          Manajemen user
        </a>
      </div>
      <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <div class="user-chip" style="cursor:pointer;" onclick="this.closest('form').submit()">
            <div class="av">{{ strtoupper(substr(auth()->user()->nama ?? 'KPH', 0, 2)) }}</div>
            <div class="info"><p>{{ auth()->user()->nama ?? 'KPH Kediri' }}</p><span>Administrator · klik untuk keluar</span></div>
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
