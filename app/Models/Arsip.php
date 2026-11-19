<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\RetensiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Arsip extends Model
{
    use HasFactory;

    protected $table = 'arsip';

    protected $fillable = [
        'uuid',
        'nomor_registrasi',
        'nomor_agenda',
        'kode_surat',
        'kode_klasifikasi',
        'perihal',
        'tahun_surat',
        'tanggal_surat',
        'pengirim',
        'penerima',
        'status_arsip',
        'kriteria_akhir',
        'sifat_naskah',
        'tingkat_perkembangan',
        'jumlah_lampiran',
        'ringkasan_disposisi',
        'depot_penyimpanan',
        'kode_rak',
        'baris_tingkat',
        'nomor_boks',
        'lokasi_status',
        'created_by',
        'is_deleted',
        'deleted_audit_id',
        'deleted_by',
        'deleted_at',
        'jra_id',
        'tanggal_acuan_retensi',
        'tanggal_inaktif',
        'tanggal_akhir_retensi',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
        'tanggal_acuan_retensi' => 'date',
        'tanggal_inaktif' => 'date',
        'tanggal_akhir_retensi' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function jra()
    {
        return $this->belongsTo(Jra::class, 'jra_id');
    }

    /** Arsip yang masa retensinya sudah habis dan berketerangan Musnah. */
    public function scopeJatuhTempoMusnah($query)
    {
        return $query->whereNotNull('jra_id')
            ->where('kriteria_akhir', 'Musnah')
            ->whereDate('tanggal_akhir_retensi', '<=', now()->toDateString());
    }

    /** Fase retensi saat ini (null bila arsip belum terhubung ke JRA). */
    public function getFaseRetensiAttribute(): ?string
    {
        if (!$this->jra) {
            return null;
        }

        return RetensiService::hitung($this->jra, $this->tanggal_surat, $this->tanggal_acuan_retensi)['fase'];
    }

    public function getFaseRetensiLabelAttribute(): ?string
    {
        return $this->fase_retensi ? RetensiService::FASE_LABEL[$this->fase_retensi] : null;
    }

    /** Isi status & tanggal retensi dari JRA (tanpa menyimpan). */
    public function terapkanRetensi(): void
    {
        if (!$this->jra) {
            return;
        }

        $h = RetensiService::hitung($this->jra, $this->tanggal_surat, $this->tanggal_acuan_retensi);
        $this->status_arsip = $h['status_arsip'];
        $this->kriteria_akhir = $h['kriteria_akhir'];
        $this->tanggal_inaktif = $h['tanggal_inaktif'];
        $this->tanggal_akhir_retensi = $h['tanggal_akhir_retensi'];
    }

    /** Hitung ulang semua arsip berJRA. Mengembalikan jumlah arsip yang berubah status. */
    public static function sinkronkanRetensi(): int
    {
        $berubah = 0;

        static::with('jra')->where('is_deleted', false)->whereNotNull('jra_id')
            ->chunkById(200, function ($chunk) use (&$berubah) {
                foreach ($chunk as $arsip) {
                    $statusLama = $arsip->status_arsip;
                    $arsip->terapkanRetensi();

                    if (!$arsip->isDirty()) {
                        continue;
                    }

                    $arsip->saveQuietly();

                    if ($statusLama !== $arsip->status_arsip) {
                        $berubah++;
                        AuditLog::create([
                            'referensi_audit' => '#RET-' . date('Ymd') . '-' . $arsip->id . '-' . substr(uniqid(), -4),
                            'aksi' => 'UPDATE',
                            'arsip_id' => $arsip->id,
                            'kode_surat' => $arsip->kode_surat,
                            'perihal' => $arsip->perihal,
                            'actor_name' => 'Sistem (JRA Otomatis)',
                            'actor_nip' => '-',
                            'actor_role' => 'sistem',
                            'detail' => "Status berubah otomatis {$statusLama} -> {$arsip->status_arsip} berdasarkan Jadwal Retensi Arsip.",
                        ]);
                    }
                }
            });

        return $berubah;
    }

    /** Sinkronisasi ringan maksimal sekali per hari (dipanggil saat dashboard/daftar dibuka). */
    public static function sinkronHarian(): void
    {
        Cache::remember('retensi_sync_' . date('Ymd'), now()->endOfDay(), fn () => static::sinkronkanRetensi() + 1);
    }
}
