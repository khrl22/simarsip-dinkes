{{-- Pemilih Jadwal Retensi Arsip (JRA) + pratinjau status otomatis.
     Variabel: $jraList (Collection<Jra>), $selectedJra (id|null), $acuan (Y-m-d|null), $required (bool) --}}
@php
    $jraJson = $jraList->map(fn ($j) => [
        'id' => $j->id, 'kode' => $j->kode, 'series' => $j->series, 'kelompok' => $j->kelompok,
        'aktif' => $j->aktif_tahun, 'inaktif' => $j->inaktif_tahun, 'pemicu' => $j->pemicu,
        'pemicu_label' => $j->pemicu_label, 'ket' => $j->keterangan, 'catatan' => $j->catatan,
    ])->values();
@endphp

<div id="retensiPicker" class="md:col-span-2 rounded-xl border border-sky-200 bg-sky-50/40 p-5 space-y-4">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-hourglass-half text-sm"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900">Klasifikasi &amp; Jadwal Retensi Arsip (JRA)</h3>
            <p class="text-[11px] text-slate-500">Status Aktif / Inaktif dan kriteria Musnah / Permanen dihitung otomatis dari JRA (Perwal Kota Bandung No. 70 Tahun 2020).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-800 mb-1.5">Jenis / Series Arsip menurut JRA @if($required)<span class="text-rose-500">*</span>@endif</label>
            <input type="text" id="jraFilter" placeholder="Ketik untuk menyaring (mis. keuangan, akreditasi, cuti)..."
                   class="w-full mb-2 px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
            <select name="jra_id" id="jraSelect" @if($required) required @endif
                    class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800"></select>
            @error('jra_id') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-800 mb-1.5">Tanggal Peristiwa Pemicu Retensi <span class="text-slate-400 font-normal">(opsional)</span></label>
            <input type="date" name="tanggal_acuan_retensi" id="tglAcuan" value="{{ old('tanggal_acuan_retensi', $acuan) }}"
                   class="w-full md:w-1/2 px-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
            <p class="text-[11px] text-slate-400 mt-1.5" id="pemicuHint">Kosongkan untuk memakai tanggal surat. Isi bila retensi dihitung dari kegiatan selesai / SK ditetapkan / peraturan baru / tidak berlaku.</p>
        </div>
    </div>

    <div id="retensiPreview" class="hidden rounded-lg bg-white border border-slate-200 p-4 text-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Masa Aktif / Inaktif</span><span id="pvMasa" class="font-bold text-slate-800"></span></div>
            <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Menjadi Inaktif</span><span id="pvInaktif" class="font-bold text-slate-800 font-mono"></span></div>
            <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Akhir Retensi</span><span id="pvAkhir" class="font-bold text-slate-800 font-mono"></span></div>
            <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Nasib Akhir</span><span id="pvKet" class="font-bold text-slate-800"></span></div>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Status saat ini:</span>
            <span id="pvStatus" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"></span>
            <span id="pvCatatan" class="text-[11px] text-slate-500"></span>
        </div>
    </div>
</div>

<script>
(function () {
    const data = @json($jraJson);
    const selected = @json((string) old('jra_id', $selectedJra));
    const sel = document.getElementById('jraSelect');
    const filter = document.getElementById('jraFilter');
    const acuan = document.getElementById('tglAcuan');
    const prev = document.getElementById('retensiPreview');
    const FASE = {
        aktif: ['Aktif', 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
        inaktif: ['Inaktif', 'bg-amber-50 text-amber-700 border border-amber-200'],
        musnah: ['Jatuh Tempo Musnah', 'bg-rose-50 text-rose-700 border border-rose-200'],
        statis: ['Serah ke Arsip Statis', 'bg-sky-50 text-sky-700 border border-sky-200'],
        nilai: ['Perlu Dinilai Kembali', 'bg-violet-50 text-violet-700 border border-violet-200'],
        orang: ['Berkas Perseorangan', 'bg-slate-100 text-slate-700 border border-slate-200'],
    };

    function build() {
        const q = filter.value.trim().toLowerCase();
        const keep = sel.value || selected;
        sel.innerHTML = '<option value="">' + (sel.required ? '— Pilih jenis arsip —' : '— Tanpa JRA (status manual) —') + '</option>';
        const groups = {};
        data.forEach(j => {
            if (q && !(j.kode + ' ' + j.series + ' ' + j.kelompok).toLowerCase().includes(q) && String(j.id) !== keep) return;
            (groups[j.kelompok] = groups[j.kelompok] || []).push(j);
        });
        Object.keys(groups).forEach(k => {
            const og = document.createElement('optgroup'); og.label = k;
            groups[k].forEach(j => {
                const o = document.createElement('option');
                o.value = j.id; o.textContent = j.kode + ' — ' + j.series + ' [' + j.aktif + 'th aktif / ' + j.inaktif + 'th inaktif / ' + j.ket + ']';
                if (String(j.id) === keep) o.selected = true;
                og.appendChild(o);
            });
            sel.appendChild(og);
        });
    }

    const fmt = d => d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    const addYears = (d, n) => { const r = new Date(d); const m = r.getMonth(); r.setFullYear(r.getFullYear() + n); if (r.getMonth() !== m) r.setDate(0); return r; };

    function toggleManual(on) {
        document.querySelectorAll('select[name=status_arsip], select[name=kriteria_akhir]').forEach(e => { e.disabled = on; e.closest('div')?.classList.toggle('opacity-50', on); });
        const kk = document.querySelector('input[name=kode_klasifikasi]');
        if (kk) { kk.readOnly = on; kk.required = !on; }
    }

    function preview() {
        const j = data.find(x => String(x.id) === sel.value);
        toggleManual(!!j);
        if (!j) { prev.classList.add('hidden'); return; }
        const kk = document.querySelector('input[name=kode_klasifikasi]');
        if (kk) kk.value = j.kode + ' - ' + j.series;

        const surat = document.querySelector('input[name=tanggal_surat]')?.value;
        const basis = acuan.value || surat;
        document.getElementById('pemicuHint').textContent = 'Pemicu menurut JRA: ' + j.pemicu_label + '. Kosongkan untuk memakai tanggal surat.';
        prev.classList.remove('hidden');
        document.getElementById('pvMasa').textContent = j.aktif + ' th / ' + j.inaktif + ' th';
        document.getElementById('pvKet').textContent = j.ket;
        document.getElementById('pvCatatan').textContent = j.catatan || '';
        const st = document.getElementById('pvStatus');

        let fase;
        if (j.ket === 'Berkas Perseorangan') {
            fase = 'orang';
            document.getElementById('pvInaktif').textContent = '-'; document.getElementById('pvAkhir').textContent = '-';
        } else if (!basis) {
            document.getElementById('pvInaktif').textContent = '-'; document.getElementById('pvAkhir').textContent = '-';
            st.textContent = 'Isi tanggal surat'; st.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600'; return;
        } else {
            let dasar = new Date(basis + 'T00:00:00');
            if (j.pemicu === 'tahun_anggaran_berakhir') dasar = new Date(dasar.getFullYear(), 11, 31);
            const inaktif = addYears(dasar, j.aktif), akhir = addYears(inaktif, j.inaktif);
            const now = new Date(); now.setHours(0, 0, 0, 0);
            document.getElementById('pvInaktif').textContent = fmt(inaktif);
            document.getElementById('pvAkhir').textContent = fmt(akhir);
            fase = now < inaktif ? 'aktif' : (now < akhir ? 'inaktif' : (j.ket === 'Musnah' ? 'musnah' : (j.ket === 'Permanen' ? 'statis' : 'nilai')));
        }
        st.textContent = FASE[fase][0];
        st.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold ' + FASE[fase][1];
    }

    filter.addEventListener('input', () => { build(); preview(); });
    sel.addEventListener('change', preview);
    acuan.addEventListener('change', preview);
    document.querySelector('input[name=tanggal_surat]')?.addEventListener('change', preview);
    document.addEventListener('DOMContentLoaded', () => { build(); preview(); });
})();
</script>
