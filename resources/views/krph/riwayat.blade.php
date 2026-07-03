@extends('layouts.krph')
@section('title', 'Riwayat Input')
@section('content')
  <div class="topbar"><div><h1>Riwayat input</h1><p class="sub">Semua data yang pernah Anda masukkan, lengkap dengan foto dokumentasi tiap inputan</p></div></div>

  <div class="panel">
    <form method="GET" class="form-grid cols-4" style="margin-bottom:0;">
      <div class="field" style="margin-bottom:0;">
        <label>Bulan</label>
        <select name="bulan" onchange="this.form.submit()">
          <option value="">Semua bulan</option>
          @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
            <option value="{{ $m }} {{ now()->year }}" @selected(request('bulan') === $m.' '.now()->year)>{{ $m }} {{ now()->year }}</option>
          @endforeach
        </select>
      </div>
      <div class="field" style="margin-bottom:0;">
        <label>Petak</label>
        <select name="petak" onchange="this.form.submit()">
          <option value="">Semua petak</option>
          @foreach ($petakList as $p)
            <option value="{{ $p->kode_petak }}" @selected(request('petak') === $p->kode_petak)>{{ $p->kode_petak }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>

  <div class="panel">
    <table id="riwayatTable">
      <tr><th>Tanggal</th><th>BKPH</th><th>RPH</th><th>Petak</th><th>Capaian</th><th>Foto</th><th>Status</th></tr>
      @foreach ($riwayat as $r)
        <tr>
          <td class="mono">{{ $r->tanggal_update->translatedFormat('d M Y') }}</td>
          <td>{{ $r->petak?->rph?->bkph?->nama_bkph }}</td>
          <td>{{ $r->petak?->rph?->nama_rph }}</td>
          <td class="mono">{{ $r->petak?->kode_petak }}</td>
          <td class="mono">{{ $r->persentase_capaian }}%</td>
          <td>
            @if ($r->dokumentasiFoto->isNotEmpty())
              <a href="{{ route('foto.show', $r->dokumentasiFoto->first()->id_foto) }}" class="thumb-sq" style="background:var(--primary-light); text-decoration:none;">📷</a>
            @else
              <span class="thumb-sq empty">—</span>
            @endif
          </td>
          <td>
            @if ($r->status_validasi === 'Valid')
              <span class="badge ok">Valid</span>
            @elseif ($r->status_validasi === 'Menunggu')
              <span class="badge pending">Menunggu</span>
            @else
              <span class="badge bad">Tidak Valid</span>
            @endif
          </td>
        </tr>
      @endforeach
    </table>
    @if ($riwayat->isEmpty())
      <p style="text-align:center; color:var(--ink-soft); padding:1rem 0;">Tidak ada data yang cocok dengan filter.</p>
    @endif
    <div style="margin-top:1rem;">{{ $riwayat->links() }}</div>
  </div>
@endsection
