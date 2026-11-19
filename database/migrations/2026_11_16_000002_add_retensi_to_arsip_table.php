<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->foreignId('jra_id')->nullable()->constrained('jra')->nullOnDelete();
            $table->date('tanggal_acuan_retensi')->nullable();  // tanggal peristiwa pemicu (opsional)
            $table->date('tanggal_inaktif')->nullable();         // mulai inaktif
            $table->date('tanggal_akhir_retensi')->nullable();   // akhir masa inaktif (jatuh tempo)
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jra_id');
            $table->dropColumn(['tanggal_acuan_retensi', 'tanggal_inaktif', 'tanggal_akhir_retensi']);
        });
    }
};
