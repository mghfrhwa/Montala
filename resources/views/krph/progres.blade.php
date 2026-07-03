@extends('layouts.krph')
@section('title', 'Progres Wilayah Saya')
@section('content')
  @php
    $dash = 2 * M_PI * 21;
    $offset = $dash - ($dash * min($realisasiPersen, 100) / 100);
  @endphp
  <div class="krph-hero">
    <div class="txt">
      <p class="eyebrow">Kerja lapangan · {{ $rph->nama_rph }}</p>
      <h1>Halo, {{ auth()->user()->nama }} 👋</h1>
      <p>Ringkasan capaian petak yang menjadi tanggung jawab Anda. Ketuk kartu petak untuk melihat foto dokumentasi.</p>
    </div>
    <div class="ring-wrap">
      <svg width="52" height="52" viewBox="0 0 52 52">
        <circle cx="26" cy="26" r="21" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="6"/>
        <circle cx="26" cy="26" r="21" fill="none" stroke="#78BC98" stroke-width="6" stroke-linecap="round" stroke-dasharray="{{ $dash }}" stroke-dashoffset="{{ $offset }}" transform="rotate(-90 26 26)"/>
      </svg>
      <div><div class="rv">{{ $realisasiPersen }}%</div><div class="rl">realisasi · target {{ $targetPersen }}%</div></div>
    </div>
  </div>

  <div class="cards-row krph-cards">
    <div class="metric-card mc-v2 mc-primary">
      <div class="icn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></div>
      <p class="label">Jumlah petak</p><p class="value">{{ $petakRows->count() }}</p>
    </div>
    <div class="metric-card mc-v2 mc-actual">
      <div class="icn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 17l6-6 4 4 8-8M21 7v6M21 7h-6"/></svg></div>
      <p class="label">Realisasi pohon bulan ini</p><p class="value">{{ number_format($totalRealPohon,0,',','.') }}</p>
      <span class="delta up">dari rencana {{ number_format($totalTargetPohon,0,',','.') }} pohon</span>
    </div>
    <div class="metric-card mc-v2 mc-good">
      <div class="icn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6L9 17l-5-5"/></svg></div>
      <p class="label">Petak selesai</p><p class="value">{{ $petakSelesai }}</p>
    </div>
    <div class="metric-card mc-v2 mc-warn">
      <div class="icn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L14.7 3.86a2 2 0 00-3.4 0z"/></svg></div>
      <p class="label">Perlu perbaikan data</p><p class="value">{{ $perluPerbaikan }}</p>
      @if ($perluPerbaikan > 0)<span class="delta warn">segera tindak lanjuti</span>@endif
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Petak di {{ $rph->nama_rph }}</h3><span class="legend-chip">📷 = foto dokumentasi tersedia</span></div>
    <div class="petak-row petak-row-v2">
      @forelse ($petakRows as $row)
        @php
          $p = $row['latest'];
          $persen = $p ? $p->persentase_capaian : 0;
          $cls = $persen >= 80 ? 'high' : ($persen >= 40 ? 'mid' : 'low');
        @endphp
        @if ($p && $p->dokumentasiFoto->isNotEmpty())
          <a href="{{ route('foto.show', $p->dokumentasiFoto->first()->id_foto) }}" class="petak petak-v2 {{ $cls }} clickable" style="text-decoration:none;">
            <div class="fill" style="height:{{ max($persen,4) }}%"></div>
            <span class="code">{{ $row['petak']->kode_petak }}</span>
            <span class="pct">{{ $persen }}%</span>
            <svg class="cam" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="7" width="16" height="12" rx="2"/><path d="M9 7l1.5-2h3L15 7"/><circle cx="12" cy="13" r="3"/></svg>
          </a>
        @else
          <div class="petak petak-v2 {{ $cls }}">
            <div class="fill" style="height:{{ max($persen,4) }}%"></div>
            <span class="code">{{ $row['petak']->kode_petak }}</span>
            <span class="pct">{{ $persen }}%</span>
          </div>
        @endif
      @empty
        <p style="color:var(--ink-soft);">Belum ada petak terdaftar di RPH ini.</p>
      @endforelse
    </div>
  </div>
@endsection
