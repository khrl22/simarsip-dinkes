<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'referensi_audit',
        'aksi',
        'arsip_id',
        'kode_surat',
        'perihal',
        'actor_name',
        'actor_nip',
        'actor_role',
        'detail',
    ];
}

