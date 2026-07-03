@extends('layouts.kph')
@section('title', 'Ringkasan Progres')
@section('content')
  <div class="topbar">
    <div>
      <h1>Ringkasan progres — {{ now()->translatedFormat('F Y') }}
        <span class="live-badge" style="font-size:0.7rem; vertical-align:middle;"><span class="live-dot"></span>Data real-time</span>
      </h1>
      <p class="sub">Naik talang tempurung, seluruh BKPH KPH Kediri — {{ $bkphCount }} BKPH · {{ $rphCount }} RPH</p>
    </div>
    <div class="date mono"><span class="live-dot"></span>Diperbarui real-time · {{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
  </div>

  <div class="cards-row">
    <div class="metric-card">
      <p class="label">Total petak dipantau <span style="color:var(--ink-faint); font-weight:400;">(real-time)</span></p>
      <p class="value">{{ $totalPetak }}</p>
      <span class="delta flat">{{ $bkphCount }} BKPH · {{ $rphCount }} RPH</span>
    </div>
    <div class="metric-card">
      <p class="label">Realisasi pohon bulan ini <span style="color:var(--ink-faint); font-weight:400;">(real-time)</span></p>
      <p class="value">{{ number_format($realisasiBulanIni, 0, ',', '.') }}</p>
      <span class="delta up">dari target {{ number_format($targetPohonBulanIni, 0, ',', '.') }} pohon</span>
    </div>
    <div class="metric-card">
      <p class="label">Petak selesai (≥80%)</p>
      <p class="value">{{ $petakSelesai }}</p>
      <span class="delta up">{{ $totalPetak > 0 ? round($petakSelesai / $totalPetak * 100) : 0 }}% dari total petak · real-time</span>
    </div>
    <div class="metric-card">
      <p class="label">Realisasi rendah (&lt;40%)</p>
      <p class="value">{{ $petakRendah }}</p>
      <span class="delta warn">perlu ditindaklanjuti — real-time</span>
    </div>
  </div>

  <div class="stack-full">
    <div class="panel" style="margin-bottom:0;">
      <div class="panel-head">
        <h3>Realisasi vs target</h3>
        <div class="legend"><span><i class="dot" style="background:var(--tgt-green)"></i>Target</span><span><i class="dot" style="background:var(--real-yellow-bar)"></i>Realisasi</span></div>
      </div>
      <div class="chart-box chart-box-full">
        @foreach ($chart as $c)
          <div class="chart-col">
            <div class="bar-pair">
              <div class="bar target" style="height:{{ max($c['target'],4) }}%"><span class="bar-val t-val">{{ $c['target'] }}%</span></div>
              @if ($c['real'] === null)
                <div class="bar actual future" style="height:4%"><span class="bar-val a-val future-val">belum</span></div>
              @else
                <div class="bar actual" style="height:{{ max($c['real'],4) }}%"><span class="bar-val a-val">{{ $c['real'] }}%</span></div>
              @endif
            </div>
            <span class="m">{{ $c['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <div class="panel" style="margin-bottom:0;">
      <div class="panel-head">
        <h3>Filter progres per petak</h3>
        <div class="legend"><span><i class="dot" style="background:#E4A377"></i>&lt;40%</span><span><i class="dot" style="background:#EDC262"></i>40–79%</span><span><i class="dot" style="background:#78BC98"></i>≥80%</span></div>
      </div>
      <p style="font-size:0.85rem; color:var(--ink-soft);">Buka menu <strong>Peta sebaran</strong> untuk rekap GPS, atau lihat rincian per RPH pada tabel di bawah.</p>
      @foreach ($bkphRows as $row)
        <div class="bkph-block">
          <p class="bkph-title">{{ $row['bkph']->nama_bkph }}</p>
          <div class="rph-row">
            <div class="petak-row">
              <span class="cell-target">Target {{ $row['target_persen'] ?? '—' }}{{ $row['target_persen'] !== null ? '%' : '' }}</span>
              <span class="cell-real">Realisasi {{ $row['realisasi_persen'] }}%</span>
              <span style="font-size:0.85rem; color:var(--ink-soft); align-self:center;">{{ number_format($row['realisasi_pohon'],0,',','.') }} / {{ number_format($row['target_pohon'],0,',','.') }} pohon</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="panel" style="margin-bottom:0;">
      <div class="panel-head"><h3>Target periode berjalan</h3><div class="legend"><span><i class="dot" style="background:var(--tgt-green)"></i>Target</span><span><i class="dot" style="background:var(--real-yellow-bar)"></i>Realisasi</span></div></div>
      <table>
        <tr><th>BKPH</th><th class="th-target">Target</th><th class="th-real">Realisasi</th></tr>
        @foreach ($bkphRows as $row)
          <tr>
            <td>{{ $row['bkph']->nama_bkph }}</td>
            <td><span class="cell-target">{{ $row['target_persen'] ?? '—' }}{{ $row['target_persen'] !== null ? '%' : '' }}</span></td>
            <td><span class="cell-real">{{ $row['realisasi_persen'] }}%</span></td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endsection
