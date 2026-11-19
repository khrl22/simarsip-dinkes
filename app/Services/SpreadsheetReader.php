<?php

namespace App\Services;

use RuntimeException;

/**
 * Pembaca spreadsheet tanpa library eksternal.
 * Mendukung: .xlsx (ZipArchive), .csv, serta .xls berisi tabel HTML (format ekspor Simarsip).
 * Hasil: array baris, tiap baris array string (kolom 0..n).
 */
class SpreadsheetReader
{
    public static function read(string $path): array
    {
        $head = (string) file_get_contents($path, false, null, 0, 8);

        if (str_starts_with($head, "\xD0\xCF\x11\xE0")) {
            throw new RuntimeException('File .xls format lama (biner) tidak bisa dibaca. Buka di Excel, lalu simpan ulang sebagai .xlsx atau .csv.');
        }
        if (str_starts_with($head, 'PK')) {
            return self::readXlsx($path);
        }

        $raw = (string) file_get_contents($path);
        if (preg_match('/<table/i', $raw)) {
            return self::readHtml($raw);
        }

        return self::readCsv($raw);
    }

    private static function readCsv(string $raw): array
    {
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
        }

        $firstLine = strtok($raw, "\n") ?: '';
        $delims = [';' => substr_count($firstLine, ';'), ',' => substr_count($firstLine, ','), "\t" => substr_count($firstLine, "\t")];
        arsort($delims);
        $delimiter = (string) array_key_first($delims);

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, $raw);
        rewind($fh);

        $rows = [];
        while (($r = fgetcsv($fh, 0, $delimiter, '"', '\\')) !== false) {
            $rows[] = array_map(fn ($v) => trim((string) $v), $r);
        }
        fclose($fh);

        return $rows;
    }

    private static function readHtml(string $raw): array
    {
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
        }

        $prev = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $raw);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $rows = [];
        foreach ($dom->getElementsByTagName('tr') as $tr) {
            $row = [];
            foreach ($tr->childNodes as $cell) {
                if (in_array(strtolower($cell->nodeName), ['td', 'th'], true)) {
                    $row[] = trim(preg_replace('/\s+/u', ' ', $cell->textContent));
                }
            }
            if ($row) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private static function readXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File Excel tidak dapat dibuka. Pastikan file tidak rusak atau diberi kata sandi.');
        }

        // Shared strings
        $shared = [];
        $ss = $zip->getFromName('xl/sharedStrings.xml');
        if ($ss !== false) {
            $x = simplexml_load_string($ss);
            if ($x !== false) {
                foreach ($x->si as $si) {
                    $t = '';
                    if (isset($si->t)) {
                        $t = (string) $si->t;
                    } else {
                        foreach ($si->r as $run) {
                            $t .= (string) $run->t;
                        }
                    }
                    $shared[] = $t;
                }
            }
        }

        // Cari sheet pertama
        $sheetPath = 'xl/worksheets/sheet1.xml';
        $wb = $zip->getFromName('xl/workbook.xml');
        $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($wb !== false && $rels !== false) {
            $w = simplexml_load_string($wb);
            $r = simplexml_load_string($rels);
            if ($w !== false && $r !== false && isset($w->sheets->sheet[0])) {
                $rid = (string) $w->sheets->sheet[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                foreach ($r->Relationship as $rel) {
                    if ((string) $rel['Id'] === $rid) {
                        $target = ltrim((string) $rel['Target'], '/');
                        $sheetPath = str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
                        break;
                    }
                }
            }
        }

        $xml = $zip->getFromName($sheetPath);
        $zip->close();
        if ($xml === false) {
            throw new RuntimeException('Lembar kerja pertama pada file Excel tidak ditemukan.');
        }

        $sheet = simplexml_load_string($xml);
        if ($sheet === false || !isset($sheet->sheetData)) {
            throw new RuntimeException('Isi file Excel tidak dapat dibaca.');
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $col = self::colIndex((string) $c['r']);
                $type = (string) $c['t'];

                if ($type === 's') {
                    $val = $shared[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = isset($c->is->t) ? (string) $c->is->t : '';
                    if ($val === '' && isset($c->is->r)) {
                        foreach ($c->is->r as $run) {
                            $val .= (string) $run->t;
                        }
                    }
                } else {
                    $val = (string) $c->v;
                }
                $cells[$col] = trim($val);
            }

            if ($cells) {
                $max = max(array_keys($cells));
                $line = [];
                for ($i = 0; $i <= $max; $i++) {
                    $line[] = $cells[$i] ?? '';
                }
                $rows[] = $line;
            } else {
                $rows[] = [];
            }
        }

        return $rows;
    }

    private static function colIndex(string $ref): int
    {
        preg_match('/^([A-Z]+)/i', $ref, $m);
        $letters = strtoupper($m[1] ?? 'A');
        $n = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $n = $n * 26 + (ord($letters[$i]) - 64);
        }

        return $n - 1;
    }
}
