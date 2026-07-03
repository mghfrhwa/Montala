@extends('layouts.kph')
@section('title', 'Target Progres')
@section('content')
  <div class="topbar"><div><h1>Target progres</h1><p class="sub">Tetapkan target realisasi bulanan (jumlah pohon) — persentase capaian dihitung otomatis begitu data realisasi lapangan masuk</p></div></div>

  <form method="POST" action="{{ route('kph.target.store') }}">
    @csrf
    <input type="hidden" name="tahun" value="{{ $tahun }}">
    <div class="panel">
      <div class="panel-head">
        <h3>Target realisasi bulanan — Tahun {{ $tahun }}</h3>
        <form method="GET" style="margin:0;">
          <select name="tahun" style="width:auto;" onchange="this.form.submit()">
            @foreach ([$tahun - 1, $tahun, $tahun + 1] as $y)
              <option value="{{ $y }}" @selected($y === $tahun)>{{ $y }}</option>
            @endforeach
          </select>
        </form>
      </div>
      <div class="target-year-legend">
        <span><i style="background:var(--surface-alt); border:1px solid var(--line-strong);"></i>Bulan berlalu — realisasi sudah masuk, % dihitung otomatis</span>
        <span><i style="background:var(--target-bg); border:1px solid #EFCFA0;"></i>Bulan berjalan</span>
        <span><i style="background:var(--surface); border:1px solid var(--line-strong);"></i>Bulan mendatang — input target (pohon)</span>
      </div>
      <div class="target-year-wrap">
        <table class="target-year-table">
          <tr>
            <th>BKPH</th>
            @foreach (['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $i => $m)
              <th class="{{ ($i+1) === $bulanBerjalan ? 'current' : '' }}">{{ $m }}</th>
            @endforeach
          </tr>
          @foreach ($rows as $row)
            <tr>
              <td>{{ $row['bkph']->nama_bkph }}</td>
              @foreach ($row['bulan'] as $m => $b)
                <td class="{{ $m === $bulanBerjalan ? 'current' : '' }}">
                  @if ($b['status'] === 'depan')
                    <div class="cell-pohon-input">
                      <input type="number" step="1" min="0" name="target[{{ $row['bkph']->id_bkph }}][{{ $m }}]" value="{{ $b['target_pohon'] }}" placeholder="—">
                      <span class="unit-sm">pohon</span>
                    </div>
                  @else
                    <span class="cell-pct">{{ $b['realisasi_persen'] ?? 0 }}%</span>
                    <div class="cell-sub">
                      @if ($b['target_pohon'] !== null)
                        {{ number_format($b['realisasi_pohon'] ?? 0, 0, ',', '.') }}/{{ number_format($b['target_pohon'], 0, ',', '.') }} pohon
                      @else
                        target belum ditetapkan
                      @endif
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </table>
      </div>
      <p class="export-note" style="margin-top:0.8rem;">Kolom bulan berlalu/berjalan menampilkan <strong>persentase capaian</strong> (realisasi pohon ÷ target pohon bulan tersebut), dihitung otomatis dari data yang diinput Asper/KRPH di menu Input Data Lapangan. Kolom bulan mendatang berisi <strong>input target (jumlah pohon)</strong> yang ditetapkan KPH.</p>
      <button class="btn-primary" type="submit" style="margin-top:1.2rem; width:auto; padding-left:1.6rem; padding-right:1.6rem;">Simpan perubahan target {{ $tahun }}</button>
    </div>
  </form>

  <div class="panel">
    <div class="panel-head"><h3>Rincian target per RPH</h3><span style="font-size:0.85rem; color:var(--ink-soft);">Pilih BKPH untuk melihat target &amp; realisasi tiap RPH bulan berjalan</span></div>
    <form method="GET" class="field" style="margin-bottom:1rem;">
      <input type="hidden" name="tahun" value="{{ $tahun }}">
      <label>BKPH</label>
      <select name="bkph" onchange="this.form.submit()">
        @foreach ($bkphList as $b)
          <option value="{{ $b->id_bkph }}" @selected($b->id_bkph === $bkphPickId)>{{ $b->nama_bkph }}</option>
        @endforeach
      </select>
    </form>
    <div class="target-row" style="border-bottom:2px solid var(--line-strong); padding-top:0;">
      <span class="name" style="color:var(--ink-faint); font-size:0.78rem; text-transform:uppercase; letter-spacing:.03em;">RPH</span>
      <span style="color:var(--ink-faint); font-size:0.78rem; text-transform:uppercase; letter-spacing:.03em;">Target pohon (bulan ini)</span>
      <span style="color:var(--ink-faint); font-size:0.78rem; text-transform:uppercase; letter-spacing:.03em;">Realisasi → persentase</span>
    </div>
    @forelse ($rphList as $r)
      <div class="target-row">
        <span class="name">{{ $r['rph']->nama_rph }}</span>
        <span>{{ $r['target_pohon'] !== null ? number_format($r['target_pohon'],0,',','.').' pohon' : 'belum ditetapkan' }}</span>
        <span class="realisasi-note {{ $r['realisasi_pohon'] === 0 ? 'pending' : '' }}">
          @if ($r['realisasi_pohon'] > 0)
            <strong>{{ number_format($r['realisasi_pohon'],0,',','.') }} pohon</strong> → {{ $r['realisasi_persen'] }}%
          @else
            belum ada data realisasi bulan ini
          @endif
        </span>
      </div>
    @empty
      <p style="color:var(--ink-soft); font-size:0.9rem;">Belum ada RPH pada BKPH ini.</p>
    @endforelse
  </div>
@endsection