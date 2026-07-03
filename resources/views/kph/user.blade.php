@extends('layouts.kph')
@section('title', 'Manajemen User')
@section('content')
  <div class="topbar">
    <div><h1>Manajemen user</h1><p class="sub">Kelola akun KRPH/Asper dan KPH yang memiliki akses sistem</p></div>
    <button class="btn-primary" type="button" style="width:auto; padding:0.7rem 1.3rem;" onclick="document.getElementById('formTambahUser').classList.toggle('open-form')">+ Tambah pengguna</button>
  </div>

  <div class="panel" id="formTambahUser" style="display:none;">
    <div class="panel-head"><h3>Tambah pengguna baru</h3></div>
    <form method="POST" action="{{ route('kph.user.store') }}" class="form-grid">
      @csrf
      <div class="field"><label>Nama</label><input type="text" name="nama" value="{{ old('nama') }}" required></div>
      <div class="field"><label>Username</label><input type="text" name="username" value="{{ old('username') }}" required></div>
      <div class="field"><label>Kata sandi</label><input type="password" name="password" required></div>
      <div class="field">
        <label>Peran</label>
        <select name="role" id="roleSelectUser" onchange="document.getElementById('rphFieldUser').style.display = this.value === 'KRPH' ? 'block' : 'none'">
          <option value="KRPH">KRPH / Asper</option>
          <option value="KPH">KPH</option>
        </select>
      </div>
      <div class="field" id="rphFieldUser">
        <label>Wilayah (RPH)</label>
        <select name="id_rph">
          @foreach ($rphList as $r)
            <option value="{{ $r->id_rph }}">{{ $r->nama_rph }} · {{ $r->bkph?->nama_bkph }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-actions"><button class="btn-primary" type="submit" style="width:auto;">Simpan pengguna</button></div>
    </form>
  </div>

  <div class="panel">
    <table id="userTable">
      <tr><th>Nama</th><th>Peran</th><th>Wilayah</th><th>Status</th><th></th></tr>
      @foreach ($users as $u)
        <tr>
          <td><div class="user-row-name"><div class="av">{{ strtoupper(substr($u->nama,0,2)) }}</div>{{ $u->nama }}</div></td>
          <td>{{ $u->role === 'KRPH' ? 'KRPH / Asper' : 'KPH' }}</td>
          <td>{{ $u->rph ? $u->rph->nama_rph.' · '.optional($u->rph->bkph)->nama_bkph : 'Seluruh wilayah' }}</td>
          <td>
            @if ($u->status_aktif)
              <span class="badge ok">Aktif</span>
            @else
              <span class="badge pending">Nonaktif</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('kph.user.toggle', $u->id_user) }}">
              @csrf @method('PATCH')
              <button class="btn-ghost" type="submit">Ubah</button>
            </form>
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <style>#formTambahUser.open-form{ display:block !important; }</style>
@endsection
