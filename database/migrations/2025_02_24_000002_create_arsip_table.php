<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // e.g. ARS-2025-08832-KU6
            $table->string('nomor_registrasi')->unique(); // e.g. REG-2025-02-0089
            $table->string('nomor_agenda')->nullable(); // e.g. #0089/AG/2025
            $table->string('kode_surat')->index(); // e.g. 005/TU/II/2025
            $table->string('kode_klasifikasi')->nullable(); // e.g. KU.01.02 - Keuangan & Anggaran (RKA/DPA)
            $table->text('perihal');
            $table->integer('tahun_surat')->index();
            $table->date('tanggal_surat');
            $table->string('pengirim');
            $table->string('penerima');
            $table->enum('status_arsip', ['Aktif', 'Inaktif'])->default('Aktif');
            $table->enum('kriteria_akhir', ['Permanen', 'Musnah'])->default('Permanen');
            $table->string('sifat_naskah')->default('Biasa / Terbuka'); // Biasa / Terbuka, Penting / Segera, Rahasia Terbatas, Sangat Rahasia
            $table->string('tingkat_perkembangan')->default('Asli (Tersedia Berkas Fisik & Digital)');
            $table->string('jumlah_lampiran')->default('1 Berkas (3 Lembar Dokumen Utama + 2 Lampiran)');
            $table->text('ringkasan_disposisi')->nullable();
            
            // Lokasi Fisik
            $table->string('depot_penyimpanan')->default('Depot Arsip Pusat (Lantai 2)');
            $table->string('kode_rak'); // e.g. Rak A-02
            $table->string('baris_tingkat'); // e.g. Baris 03 / Tingkat 3 (B03)
            $table->string('nomor_boks'); // e.g. Boks #14
            $table->string('lokasi_status')->default('Tersimpan'); // 'Tersimpan', 'Lokasi Dikosongkan'
            
            // Audit info
            $table->string('created_by')->default('Budi Santoso (Administrator)');
            $table->boolean('is_deleted')->default(false)->index();
            $table->string('deleted_audit_id')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip');
    }
};

