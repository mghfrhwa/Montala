@extends('layouts.kph')
@section('title', 'Peta Sebaran')
@section('content')
  <div class="topbar"><div><h1>Peta sebaran</h1><p class="sub">Lokasi {{ $bkphList->count() }} BKPH KPH Kediri lengkap dengan koordinat GPS dan tingkat capaian</p></div></div>

  <div class="map-wrap">
    <div>
      <div id="leafletMap" style="display:flex; align-items:center; justify-content:center; background:var(--surface-alt); color:var(--ink-faint); font-size:0.9rem;">
        Peta interaktif (Leaflet) — hubungkan API key / tile provider di halaman ini bila diperlukan.
      </div>
      <p style="font-size:0.85rem; color:var(--ink-soft); margin-top:0.6rem;">Penanda menunjukkan titik koordinat wilayah kerja tiap BKPH — koordinat diambil dari data <code>latitude</code>/<code>longitude</code> tabel BKPH. Titik GPS per petak dapat ditambahkan begitu perangkat GPS lapangan terhubung ke sistem.</p>
    </div>
    <div class="panel" style="margin-bottom:0;">
      <div class="panel-head"><h3>Rekap wilayah &amp; koordinat GPS</h3></div>
      <table id="gpsTable">
        <tr><th>BKPH</th><th>Luas</th><th>Realisasi</th><th>GPS</th></tr>
        @foreach ($bkphList as $bkph)
          <tr>
            <td>{{ $bkph->nama_bkph }}</td>
            <td>{{ number_format($bkph->luas_ha, 2, ',', '.') }} Ha</td>
            <td><span class="cell-real">{{ $bkph->realisasi_persen }}%</span></td>
            <td>
              @if ($bkph->latitude && $bkph->longitude)
                <span class="gps-chip">{{ $bkph->latitude }}, {{ $bkph->longitude }}</span>
              @else
                <span class="gps-chip">belum ada</span>
              @endif
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endsection
