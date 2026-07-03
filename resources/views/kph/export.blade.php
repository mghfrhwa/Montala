@extends('layouts.kph')
@section('title', 'Export Laporan')
@section('content')
  <div class="topbar"><div><h1>Export laporan</h1><p class="sub">Satu pusat unduh laporan (.csv, dibuka native oleh Excel) untuk seluruh rekap target vs realisasi KPH Kediri</p></div></div>

  <div class="panel">
    <div class="panel-head"><h3>Aktivitas input terbaru</h3></div>
    <table>
      <tr><th>Petak</th><th>RPH</th><th>Diinput oleh</th><th>Realisasi</th><th>Foto</th><th>Status</th></tr>
      @forelse ($aktivitasTerbaru as $r)
        <tr>
          <td class="mono">{{ $r->petak?->kode_petak }}</td>
          <td>{{ $r->petak?->rph?->nama_rph }}</td>
          <td>{{ $r->user?->nama }}</td>
          <td>{{ $r->persentase_capaian }}%</td>
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
              <span class="badge pending">Menunggu foto</span>
            @else
              <span class="badge bad">Tidak Valid</span>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center; color:var(--ink-soft);">Belum ada aktivitas input.</td></tr>
      @endforelse
    </table>
  </div>

  <div class="export-hint">Semua export laporan (rekap KPH, per BKPH, per RPH, hingga per petak) diunduh dalam format CSV dan kini terpusat di halaman ini — gunakan filter di bawah untuk menentukan cakupan datanya.</div>

  <form method="GET" action="{{ route('kph.export.unduh') }}">
    <div class="panel">
      <div class="panel-head"><h3>1. Cakupan laporan</h3></div>
      <div class="form-grid" style="margin-bottom:1rem;">
        <div class="field" style="margin-bottom:0;">
          <label>Tingkat</label>
          <select name="level">
            <option value="kph">Rekap seluruh KPH Kediri</option>
            <option value="bkph">Per BKPH</option>
            <option value="rph">Per RPH</option>
          </select>
        </div>
        <div class="field" style="margin-bottom:0;">
          <label>BKPH</label>
          <select name="id_bkph">
            <option value="">Semua BKPH ({{ $bkphList->count() }})</option>
            @foreach ($bkphList as $b)
              <option value="{{ $b->id_bkph }}">{{ $b->nama_bkph }}</option>
            @endforeach
          </select>
        </div>
        <div class="field" style="margin-bottom:0;">
          <label>RPH</label>
          <select name="id_rph">
            <option value="">Semua RPH</option>
            @foreach ($rphList as $r)
              <option value="{{ $r->id_rph }}">{{ $r->nama_rph }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="panel-head"><h3>2. Periode &amp; status</h3></div>
      <div class="form-grid" style="margin-bottom:1.2rem;">
        <div class="field" style="margin-bottom:0;"><label>Dari tanggal</label><input type="date" name="dari" value="{{ now()->startOfMonth()->format('Y-m-d') }}"></div>
        <div class="field" style="margin-bottom:0;"><label>Sampai tanggal</label><input type="date" name="sampai" value="{{ now()->endOfMonth()->format('Y-m-d') }}"></div>
        <div class="field" style="margin-bottom:0;">
          <label>Status</label>
          <select name="status">
            <option value="">Semua status</option>
            <option value="selesai">Selesai (≥80%)</option>
            <option value="berjalan">Berjalan (40–79%)</option>
            <option value="belum">Belum mulai (&lt;40%)</option>
          </select>
        </div>
      </div>
      <button class="btn-primary" type="submit" style="width:auto; padding-left:1.8rem; padding-right:1.8rem;">📊 Unduh laporan (.csv)</button>
    </div>
  </form>
@endsection
