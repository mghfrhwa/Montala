<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — SITALANG</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/sitalang.css') }}">
</head>
<body>

<section class="view active" id="view-login">
  <div class="login-wrap">
    <div class="login-brand">
      <div>
        <div class="brand-mark"><div class="stake"></div><span>SITALANG</span></div>
        <h1>Progres naik talang tempurung, terpantau per petak.</h1>
        <p>Sistem informasi dashboard untuk KPH Kediri — mencatat realisasi lapangan dan memantau capaian terhadap target, dari petak sampai rekap kesatuan.</p>
      </div>
      <div class="foot mono">KPH KEDIRI · PERHUTANI</div>
      <div class="mosaic-bg">
        <div></div><div></div><div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div><div></div><div></div>
      </div>
    </div>
    <div class="login-form">
      <p class="eyebrow">Masuk sistem</p>
      <h2>Selamat datang kembali</h2>

      @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
      @endif
      @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('login.store') }}" id="loginForm">
        @csrf
        <input type="hidden" name="role" id="roleInput" value="KRPH">

        <div class="role-toggle">
          <button type="button" class="active" id="role-krph" onclick="setRole('KRPH', this)">KRPH / Asper</button>
          <button type="button" id="role-kph" onclick="setRole('KPH', this)">KPH</button>
        </div>

        <div class="field">
          <label for="username">NIK / Username</label>
          <input type="text" id="username" name="username" placeholder="Contoh: krph.kalipang" value="{{ old('username') }}" autofocus>
        </div>
        <div class="field">
          <label for="password">Kata sandi</label>
          <input type="password" id="password" name="password" placeholder="••••••••">
        </div>
        <button class="btn-primary" type="submit">Masuk</button>
        <p class="login-hint">Lupa kata sandi? Hubungi administrator sistem di kantor KPH Kediri.<br>Akun KRPH/Asper hanya dapat mengakses input dan progres wilayah kerjanya sendiri.</p>
      </form>
    </div>
  </div>
</section>

<script>
  function setRole(role, btn){
    document.getElementById('roleInput').value = role;
    document.getElementById('role-krph').classList.toggle('active', role === 'KRPH');
    document.getElementById('role-kph').classList.toggle('active', role === 'KPH');
  }
</script>
</body>
</html>
