<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jra extends Model
{
    protected $table = 'jra';

    protected $fillable = [
        'kode', 'lampiran', 'kelompok', 'series', 'uraian',
        'aktif_tahun', 'inaktif_tahun', 'pemicu', 'keterangan', 'catatan',
    ];

    public const PEMICU_LABEL = [
        'tanggal_surat'           => 'Tanggal surat',
        'kegiatan_selesai'        => 'Setelah pelaksanaan kegiatan selesai',
        'tahun_anggaran_berakhir' => 'Setelah tahun anggaran berakhir',
        'ditetapkan'              => 'Setelah SK/keputusan ditetapkan',
        'peraturan_baru'          => 'Setelah ditetapkan peraturan baru',
        'tidak_berlaku'           => 'Setelah tidak berlaku',
    ];

    public function getPemicuLabelAttribute(): string
    {
        return self::PEMICU_LABEL[$this->pemicu] ?? $this->pemicu;
    }

    public function getLabelAttribute(): string
    {
        return "{$this->kode} - {$this->series}";
    }
}
