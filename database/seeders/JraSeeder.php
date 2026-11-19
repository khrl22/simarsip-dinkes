<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JraSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/jra_perwal_70_2020.json');
        $rows = json_decode(file_get_contents($path), true) ?: [];
        $now = now();

        foreach ($rows as $r) {
            DB::table('jra')->updateOrInsert(
                ['kode' => $r['kode']],
                [
                    'lampiran'      => $r['lampiran'],
                    'kelompok'      => $r['kelompok'],
                    'series'        => $r['series'],
                    'uraian'        => $r['uraian'] ?: null,
                    'aktif_tahun'   => $r['aktif_tahun'],
                    'inaktif_tahun' => $r['inaktif_tahun'],
                    'pemicu'        => $r['pemicu'],
                    'keterangan'    => $r['keterangan'],
                    'catatan'       => $r['catatan'] ?? null,
                    'updated_at'    => $now,
                    'created_at'    => $now,
                ]
            );
        }
    }
}
