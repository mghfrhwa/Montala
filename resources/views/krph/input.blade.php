@extends('layouts.krph')
@section('title', 'Input Data Lapangan')
@section('content')
  <div class="topbar">
    <div><h1>Input realisasi progres</h1><p class="sub">Naik talang tempurung — petak di wilayah kerja Anda</p></div>
    <div class="date mono">{{ now()->translatedFormat('d M Y') }}</div>
  </div>

  <div class="grid-2col" style="grid-template-columns: 1.4fr 1fr;">
    <div>
      <div class="panel">
        <div class="panel-head"><h3>Formulir input petak</h3></div>

        <form method="GET" id="petakPickForm" style="margin-bottom:0.9rem;">
          <div class="field" style="margin-bottom:0;">
            <label>Petak</label>
            <select name="petak" onchange="this.form.submit()">
              @foreach ($petakList as $p)
                <option value="{{ $p->id_petak }}" @selected($petakSelected && $petakSelected->id_petak === $p->id_petak)>
                  {{ $p->kode_petak }} — {{ $p->total_pohon ?? 0 }} pohon
                </option>
              @endforeach
            </select>
          </div>
        </form>

        @if ($latestPetakStatus && $latestPetakStatus->status_validasi === 'Tidak Valid')
          <p class="capaian-result" style="background:var(--danger-bg); color:var(--danger);">Input terakhir petak ini <strong>tidak valid</strong>: {{ $latestPetakStatus->alasan_tidak_valid ?? 'data tidak lengkap' }}. Silakan lengkapi ulang di bawah.</p>
        @endif

        <form method="POST" action="{{ route('krph.input.store') }}" enctype="multipart/form-data" class="form-grid">
          @csrf
          <input type="hidden" name="id_petak" value="{{ $petakSelected?->id_petak }}">

          <div class="field full" style="margin-bottom:0;">
            <label>BKPH / RPH</label>
            <div class="readonly-box">{{ $rph->bkph?->nama_bkph }} · {{ $rph->nama_rph }}</div>
          </div>

          <div class="field full" style="margin-bottom:0;">
            <label>Mandor sadap / alur kerja</label>
            <select name="id_mandor">
              <option value="">— pilih mandor —</option>
              @foreach ($mandorList as $m)
                <option value="{{ $m->id_mandor }}">{{ $m->nama_mandor }} — {{ $m->alur_kerja }}</option>
              @endforeach
            </select>
          </div>

          <div class="field-inline">
            <div class="field"><label>Tanggal update</label><input type="date" name="tanggal_update" value="{{ now()->format('Y-m-d') }}" required></div>
            <div class="target-badge" title="Target periode berjalan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
              Target: {{ $targetPersen }}% — periode {{ now()->translatedFormat('F Y') }}
            </div>
          </div>

          <div class="field capaian-block" style="margin-bottom:0;">
            <label>Realisasi</label>
            <div class="capaian-grid">
              <div>
                <label style="font-size:0.85rem; font-weight:500; color:var(--ink-soft);">Jumlah pohon terealisasi</label>
                <input type="number" name="jumlah_pohon_realisasi" id="pohonRealisasi" min="0" value="{{ old('jumlah_pohon_realisasi') }}" oninput="hitungCapaian()" required>
              </div>
              <div>
                <label style="font-size:0.85rem; font-weight:500; color:var(--ink-soft);">Rencana pohon petak ini</label>
                <div class="rencana-display mono" id="rencanaDisplay">{{ $petakSelected?->total_pohon ?? 0 }} pohon</div>
              </div>
            </div>
            <span class="capaian-result" id="capaianHasil">—</span>
          </div>

          <div class="field full" style="margin-bottom:0; grid-column:2/4;">
            <label>Dokumentasi foto <span style="color:var(--danger); font-weight:400;">(wajib)</span></label>
            <input type="file" name="foto" accept="image/*" required>
          </div>

          <div class="field full" style="margin-bottom:0;">
            <label>Catatan lapangan (opsional)</label>
            <textarea name="catatan_lapangan">{{ old('catatan_lapangan') }}</textarea>
          </div>

          <div class="form-actions">
            <button class="btn-primary" style="width:auto; flex:1;" type="submit">Simpan data</button>
            <button class="btn-secondary" type="reset">Batal</button>
          </div>
        </form>
      </div>
    </div>

    <div>
      <div class="panel">
        <div class="panel-head"><h3>Status input terakhir</h3></div>
        @forelse ($statusTerakhir as $s)
          <div class="status-card {{ $s->status_validasi === 'Tidak Valid' ? 'invalid' : 'valid' }}">
            <div class="icn">
              @if ($s->status_validasi === 'Tidak Valid')
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
              @else
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6L9 17l-5-5"/></svg>
              @endif
            </div>
            <div>
              <h4>Petak {{ $s->petak?->kode_petak }} — {{ $s->status_validasi === 'Tidak Valid' ? 'data tidak valid' : 'tersimpan' }}</h4>
              <p>Capaian {{ $s->persentase_capaian }}% tercatat pada {{ $s->tanggal_update->translatedFormat('d M Y') }}.
                @if ($s->status_validasi === 'Tidak Valid') {{ $s->alasan_tidak_valid ?? 'Data belum lengkap.' }} @endif
              </p>
              @if ($s->status_validasi === 'Tidak Valid')
                <div class="action"><a class="btn-ghost" href="{{ route('krph.input', ['petak' => $s->id_petak]) }}">Perbaiki data</a></div>
              @endif
            </div>
          </div>
        @empty
          <p style="color:var(--ink-soft); font-size:0.9rem;">Belum ada data yang diinput.</p>
        @endforelse
      </div>

      <div class="panel">
        <div class="panel-head"><h3>Petak di {{ $rph->nama_rph }}</h3></div>
        <a class="btn-export" href="{{ route('kph.export.unduh', ['id_rph' => $rph->id_rph]) }}" @if(auth()->user()->isKrph()) style="pointer-events:none; opacity:.5;" title="Ekspor tersedia di menu KPH" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3v12m0 0l-4-4m4 4l4-4"/><path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
          Ekspor ke Excel (.csv)
        </a>
        <table>
          <tr><th>Petak</th><th>Target</th><th>Realisasi</th><th>Status</th></tr>
          @foreach ($petakList as $p)
            @php $latest = $p->realisasi()->orderByDesc('tanggal_update')->orderByDesc('versi_input')->first(); @endphp
            <tr>
              <td class="mono">{{ $p->kode_petak }}</td>
              <td class="mono">{{ $targetPersen }}%</td>
              <td class="mono">{{ $latest?->persentase_capaian ?? '—' }}{{ $latest ? '%' : '' }}</td>
              <td>
                @if (!$latest)
                  <span class="status-label" style="color:var(--ink-faint);">Belum diinput</span>
                @elseif ($latest->status_validasi === 'Valid')
                  <span class="status-label ok">Tersimpan</span>
                @else
                  <span class="status-label bad">Tidak Valid</span>
                @endif
              </td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    function hitungCapaian(){
      const rencana = {{ $petakSelected?->total_pohon ?? 0 }};
      const real = parseInt(document.getElementById('pohonRealisasi').value || '0', 10);
      const hasil = document.getElementById('capaianHasil');
      if (rencana <= 0){ hasil.textContent = 'Rencana pohon petak ini belum diisi KPH.'; return; }
      const pct = Math.round(real / rencana * 100);
      const selisih = real - rencana;
      hasil.textContent = `Persentase Capaian: ${pct}% (selisih ${selisih >= 0 ? '+' : ''}${selisih} pohon dari rencana)`;
    }
    hitungCapaian();
  </script>
  @endpush
@endsection
