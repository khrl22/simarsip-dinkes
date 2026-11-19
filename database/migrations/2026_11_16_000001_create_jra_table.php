<?php

use Database\Seeders\JraSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel Jadwal Retensi Arsip (JRA) - Perwal Kota Bandung No. 70 Tahun 2020.
 * Data awal dimuat dari database/data/jra_perwal_70_2020.json.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jra', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 12)->unique();          // mis. A.002, C.015
            $table->string('lampiran', 1)->index();         // A, B, C, D
            $table->string('kelompok');                     // nama lampiran / urusan
            $table->string('series', 255);                  // jenis / series arsip
            $table->text('uraian')->nullable();             // rincian jenis arsip
            $table->unsignedSmallInteger('aktif_tahun');    // jangka simpan aktif (tahun)
            $table->unsignedSmallInteger('inaktif_tahun');  // jangka simpan inaktif (tahun)
            // tanggal_surat | kegiatan_selesai | tahun_anggaran_berakhir | ditetapkan | peraturan_baru | tidak_berlaku
            $table->string('pemicu', 40)->default('tanggal_surat');
            // Musnah | Permanen | Dinilai Kembali | Berkas Perseorangan
            $table->string('keterangan', 30)->index();
            $table->text('catatan')->nullable();            // mis. "Musnah, kecuali SK masuk berkas perseorangan"
            $table->timestamps();
        });

        (new JraSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('jra');
    }
};
