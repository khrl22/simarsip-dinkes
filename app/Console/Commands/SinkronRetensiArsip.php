<?php

namespace App\Console\Commands;

use App\Models\Arsip;
use Illuminate\Console\Command;

class SinkronRetensiArsip extends Command
{
    protected $signature = 'arsip:sinkron-retensi';

    protected $description = 'Perbarui status Aktif/Inaktif arsip otomatis berdasarkan Jadwal Retensi Arsip (JRA).';

    public function handle(): int
    {
        $berubah = Arsip::sinkronkanRetensi();
        $this->info("Sinkronisasi retensi selesai. {$berubah} arsip berubah status.");

        return self::SUCCESS;
    }
}
