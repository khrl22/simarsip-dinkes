<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('referensi_audit')->unique(); // e.g. #DEL-20240115-089, #REG-202502-0089
            $table->string('aksi'); // CREATE, UPDATE, DELETE
            $table->unsignedBigInteger('arsip_id')->nullable();
            $table->string('kode_surat');
            $table->text('perihal');
            $table->string('actor_name');
            $table->string('actor_nip');
            $table->string('actor_role');
            $table->text('detail')->nullable();
            $table->timestamps();
        });

        Schema::create('bantuan_resets', function (Blueprint $table) {
            $table->id();
            $table->string('username_or_nip');
            $table->string('status')->default('menunggu');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('bantuan_resets');
    }
};

