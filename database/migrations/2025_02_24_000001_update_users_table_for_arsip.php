<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->unique()->after('id');
            $table->string('username', 50)->nullable()->unique()->after('nip');
            $table->string('role', 20)->default('admin')->after('password'); // 'admin', 'staf'
            $table->string('jabatan')->default('Administrator Sistem')->after('role');
            $table->integer('failed_attempts')->default(0)->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'username', 'role', 'jabatan', 'failed_attempts']);
        });
    }
};

