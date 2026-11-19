<!-- MODAL KONFIRMASI HAPUS DATA ARSIP (Image 2 batch 2) -->
<div id="modalConfirmDelete" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 transform transition-all animate-scale-up">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg border border-rose-100">
                    <i class="fa-regular fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Konfirmasi Hapus Data Arsip</h3>
                    <p class="text-xs text-slate-500">Tindakan ini memerlukan otoritas Administrator</p>
                </div>
            </div>
            <button type="button" onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="deleteForm" method="POST" action="" class="p-6 space-y-4">
            @csrf

            <!-- Metadata Box -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold uppercase tracking-wider text-[10px]"># KODE SURAT</span>
                    <span id="delModalKodeSurat" class="font-mono font-bold bg-[#0d2847] text-white px-2.5 py-0.5 rounded text-xs">-</span>
                </div>

                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">PERIHAL SURAT</span>
                    <p id="delModalPerihal" class="font-bold text-slate-900 text-sm mt-0.5 leading-snug">-</p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">TANGGAL SURAT</span>
                        <p id="delModalTanggal" class="font-semibold text-slate-700 mt-0.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-slate-400"></i>
                            <span>-</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">TAHUN SURAT</span>
                        <p id="delModalTahun" class="font-semibold text-slate-700 mt-0.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-check text-slate-400"></i>
                            <span>-</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">DARI SIAPA / PENGIRIM</span>
                        <p id="delModalPengirim" class="font-medium text-slate-700 mt-0.5 truncate flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-slate-400 text-[10px]"></i>
                            <span>-</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">KEPADA SIAPA / PENERIMA</span>
                        <p id="delModalPenerima" class="font-medium text-slate-700 mt-0.5 truncate flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope-open-text text-slate-400 text-[10px]"></i>
                            <span>-</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">STATUS ARSIP</span>
                        <p id="delModalStatus" class="font-semibold text-sky-700 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-box text-sky-500"></i>
                            <span>-</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">KRITERIA AKHIR ARSIP</span>
                        <p id="delModalKriteria" class="font-semibold text-rose-600 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-shield-halved text-rose-500"></i>
                            <span>-</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Lokasi Fisik Box -->
            <div class="p-3.5 rounded-xl border border-sky-100 bg-sky-50/40 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-sky-100 text-sky-900 font-bold">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-boxes-stacked text-sky-600"></i>
                        <span>Lokasi / Letak Fisik Berkas</span>
                    </span>
                    <span class="text-slate-500 text-[11px] font-normal">Ruang Arsip</span>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div class="bg-white p-2 rounded-lg border border-sky-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">KODE RAK</span>
                        <span id="delModalRak" class="font-bold text-slate-800 text-xs block">-</span>
                        <span class="text-[10px] text-slate-400 block">Lemari / Rak</span>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-sky-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">BARIS / TINGKAT</span>
                        <span id="delModalBaris" class="font-bold text-slate-800 text-xs block">-</span>
                        <span class="text-[10px] text-slate-400 block">Baris tempat</span>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-sky-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">NOMOR DUS / BOKS</span>
                        <span id="delModalBoks" class="font-bold text-slate-800 text-xs block">-</span>
                        <span class="text-[10px] text-slate-400 block">Boks Arsip</span>
                    </div>
                </div>
            </div>

            <!-- Warning Notice -->
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base mt-0.5 flex-shrink-0"></i>
                <div class="text-[11px] leading-relaxed">
                    <strong class="font-bold">Perhatian:</strong> Menghapus data arsip ini akan melenyapkan metadata dokumen serta menghapus relasi berkas naskah digital secara permanen dari sistem dan lokasi penyimpanan.
                </div>
            </div>

            <!-- Confirmation Checkbox -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 select-none">
                    <input type="checkbox" 
                           id="confirmDeleteCheckbox" 
                           onchange="toggleDeleteButton()"
                           class="w-4 h-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                    <span>Saya mengonfirmasi untuk menghapus seluruh data arsip dan lampiran berkas ini.</span>
                </label>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        onclick="closeDeleteModal()" 
                        class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitDelete" 
                        disabled
                        class="px-4 py-2 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-all flex items-center gap-1.5 opacity-50 cursor-not-allowed">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>Ya, Hapus Arsip</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(item) {
        document.getElementById('modalConfirmDelete').classList.remove('hidden');
        document.getElementById('modalConfirmDelete').classList.add('flex');
        
        // Populate fields
        document.getElementById('deleteForm').action = "/arsip/" + item.id + "/delete";
        document.getElementById('delModalKodeSurat').innerText = item.kode_surat || '-';
        document.getElementById('delModalPerihal').innerText = item.perihal || '-';
        
        let tglFormatted = item.tanggal_surat;
        if (item.tanggal_surat) {
            const d = new Date(item.tanggal_surat);
            tglFormatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
        document.getElementById('delModalTanggal').querySelector('span').innerText = tglFormatted || '-';
        document.getElementById('delModalTahun').querySelector('span').innerText = item.tahun_surat || '-';
        document.getElementById('delModalPengirim').querySelector('span').innerText = item.pengirim || '-';
        document.getElementById('delModalPenerima').querySelector('span').innerText = item.penerima || '-';
        document.getElementById('delModalStatus').querySelector('span').innerText = (item.status_arsip || 'Aktif') + ' (Masih digunakan operasional harian)';
        document.getElementById('delModalKriteria').querySelector('span').innerText = (item.kriteria_akhir || 'Musnah') + ' (Dapat dihancurkan sesuai jadwal retensi)';
        
        document.getElementById('delModalRak').innerText = item.kode_rak || '-';
        document.getElementById('delModalBaris').innerText = item.baris_tingkat || '-';
        document.getElementById('delModalBoks').innerText = item.nomor_boks || '-';

        // Reset checkbox & button
        const chk = document.getElementById('confirmDeleteCheckbox');
        chk.checked = false;
        toggleDeleteButton();
    }

    function closeDeleteModal() {
        document.getElementById('modalConfirmDelete').classList.add('hidden');
        document.getElementById('modalConfirmDelete').classList.remove('flex');
    }

    function toggleDeleteButton() {
        const chk = document.getElementById('confirmDeleteCheckbox');
        const btn = document.getElementById('btnSubmitDelete');
        if (chk.checked) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
</script>

