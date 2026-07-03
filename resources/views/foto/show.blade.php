@extends(auth()->user()->isKph() ? 'layouts.kph' : 'layouts.krph')
@section('title', 'Dokumentasi Foto')
@section('content')
  <div class="topbar">
    <div>
      <h1>Dokumentasi foto — Petak {{ $realisasi->petak?->kode_petak }}</h1>
      <p class="sub">{{ $realisasi->petak?->rph?->bkph?->nama_bkph }} · {{ $realisasi->petak?->rph?->nama_rph }}</p>
    </div>
  </div>

  <div class="panel" style="max-width:30rem;">
    <img src="{{ $dokumentasiFoto->url }}" alt="Dokumentasi foto" style="width:100%; border-radius:0.7rem; margin-bottom:1rem;">
    <h4 style="margin:0 0 0.3rem;">{{ $dokumentasiFoto->keterangan ?? 'Dokumentasi lapangan' }}</h4>
    <p style="margin:0 0 0.15rem; color:var(--ink-soft); font-size:0.9rem;">Diunggah {{ $dokumentasiFoto->tanggal_upload->translatedFormat('d M Y, H:i') }} WIB oleh {{ $realisasi->user?->nama }}</p>
    <p style="margin:0.6rem 0 0; font-size:0.9rem;">Capaian: <strong>{{ $realisasi->persentase_capaian }}%</strong> ({{ $realisasi->jumlah_pohon_realisasi }} / {{ $realisasi->total_pohon_petak }} pohon)</p>
    @if ($realisasi->catatan_lapangan)
      <p style="margin:0.4rem 0 0; font-size:0.88rem; color:var(--ink-soft);">Catatan: {{ $realisasi->catatan_lapangan }}</p>
    @endif
  </div>
@endsection
