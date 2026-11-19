<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BantuanReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'username_or_nip',
        'status',
        'keterangan',
    ];
}

