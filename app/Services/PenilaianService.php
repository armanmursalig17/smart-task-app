<?php

namespace App\Services;

use App\Models\TerbitanTugas;
use App\Models\Soal;
use Illuminate\Support\Str;

class PenilaianService
{

    protected const MAX_SCORE_URAIAN_PER_SOAL = 10.0;
    protected const MIN_SCORE_URAIAN_PER_SOAL = 1.0;

    /**
     * Menghitung nilai otomatis (Pilihan Ganda + Uraian) dan menentukan status penilaian.
     *
     * @param TerbitanTugas $terbitan
     * @param array $jawabanSiswa Array ['soal_id' => 'jawaban']
     * @return array [nilai_otomatis (PG + Uraian), status_penilaian]
     */
    public function hitungNilaiOtomatis(TerbitanTugas $terbitan, array $jawabanSiswa): array
    {
        $allSoals = $terbitan->tugas->soals;

        $soalPG = $allSoals->where('tipe_soal_di_tugas', 'pilihan_ganda');
        $soalUraian = $allSoals->where('tipe_soal_di_tugas', 'uraian');

        $totalPG = $soalPG->count();
        $totalUraian = $soalUraian->count();

        $correctPG = 0;
        $achievedUraianPoints = 0.0;
        $maxUraianPoints = $totalUraian * self::MAX_SCORE_URAIAN_PER_SOAL;

        // 1. Hitung Jawaban Benar Pilihan Ganda (PG)
        foreach ($soalPG as $soal) {
            $siswaJawab = $jawabanSiswa[$soal->id] ?? null;
            $kunci = $soal->kunci_jawaban;

            if (!is_null($siswaJawab) && (string)$siswaJawab === (string)$kunci) {
                $correctPG++;
            }
        }

        // 2. Hitung Poin Uraian (Berdasarkan Skor Gradual 1-10)
        foreach ($soalUraian as $soal) {
            $siswaJawab = $jawabanSiswa[$soal->id] ?? '';
            $kunci = $soal->kunci_jawaban ?? '';

            // Cek apakah siswa mengisi jawaban
            if (!empty(trim($siswaJawab))) {
                if (!empty($kunci)) {
                    // Mendapatkan rasio kecocokan (0.0 - 1.0)
                    $matchRatio = $this->getUraianKeywordMatch($siswaJawab, $kunci);

                    // Menghitung skor secara gradual dari 1 hingga 10.
                    // Jika matchRatio = 0, skor = 1 (MIN_SCORE)
                    // Jika matchRatio = 1, skor = 10 (MAX_SCORE)
                    $scoreRange = self::MAX_SCORE_URAIAN_PER_SOAL - self::MIN_SCORE_URAIAN_PER_SOAL; // 9 poin

                    // Skor dihitung: MIN_SCORE + (Range * Rasio Kecocokan)
                    // Contoh: 1 + (9 * 0.8) = 1 + 7.2 = 8.2
                    $score = self::MIN_SCORE_URAIAN_PER_SOAL + ($scoreRange * $matchRatio);

                    $achievedUraianPoints += $score;
                } else {
                    // Jika kunci jawaban kosong, berikan skor minimal (1 poin)
                    $achievedUraianPoints += self::MIN_SCORE_URAIAN_PER_SOAL;
                }
            }
            // Jika siswa tidak menjawab (empty(trim($siswaJawab))), poin = 0
        }

        // 3. Tentukan Nilai dan Status
        $nilaiOtomatis = 0.0;
        $statusPenilaian = 'sudah_dinilai';

        // Hitung skor PG murni (skala 100)
        $skorPG = $totalPG > 0 ? ($correctPG / $totalPG) * 100.0 : 0.0;

        // Hitung skor Uraian murni (skala 100). Max Uraian Points adalah total poin maks (10 * jumlah soal).
        $skorUraian = $maxUraianPoints > 0 ? ($achievedUraianPoints / $maxUraianPoints) * 100.0 : 0.0;

        $jenisTugas = $terbitan->tugas->jenis_tugas;

        if ($jenisTugas === 'gabungan') {
            // Tugas Gabungan: Bobot 70% PG, 30% Uraian
            $nilaiPGBobot = $skorPG * 0.70;
            $nilaiUraianBobot = $skorUraian * 0.30;

            $nilaiOtomatis = $nilaiPGBobot + $nilaiUraianBobot;
        } elseif ($totalPG > 0 && $totalUraian === 0) {
            // Hanya PG: Bobot 100%
            $nilaiOtomatis = $skorPG;
        } elseif ($totalUraian > 0 && $totalPG === 0) {
            // Hanya Uraian: Bobot 100%
            $nilaiOtomatis = $skorUraian;
        }


        return [
            'nilai_otomatis' => round($nilaiOtomatis, 2),
            'status_penilaian' => $statusPenilaian
        ];
    }

    /**
     * Menghitung rasio kecocokan kata kunci antara jawaban siswa dan kunci.
     * Menggunakan metode sederhana tokenisasi kata.
     *
     * @param string $siswaJawab
     * @param string $kunci
     * @return float Rasio kecocokan (0.0 - 1.0)
     */
    /**
     * Mengukur kecocokan jawaban uraian secara fleksibel:
     * - Case-insensitive (abaikan huruf kapital)
     * - LIKE (str_contains)
     * - Toleransi typo (Levenshtein)
     * - Rasio kecocokan (similarity)
     */
    protected function getUraianKeywordMatch(string $siswaJawab, string $kunci): float
    {
        // Normalisasi: abaikan huruf besar, tanda baca, dan spasi berlebih
        $cleanKunci = strtolower(preg_replace('/[^a-z0-9\s]/', '', $kunci));
        $cleanJawab = strtolower(preg_replace('/[^a-z0-9\s]/', '', $siswaJawab));

        similar_text($cleanKunci, $cleanJawab, $fullPercent);
        if ($fullPercent >= 85) {
            return 1.0;
        }
        
        // Tokenisasi kata
        $kunciWords = array_unique(array_filter(explode(' ', $cleanKunci)));
        $jawabWords = array_unique(array_filter(explode(' ', $cleanJawab)));

        if (empty($kunciWords)) {
            return 1.0; // Jika kunci kosong → dianggap benar
        }

        $matchCount = 0;

        foreach ($kunciWords as $kataKunci) {
            foreach ($jawabWords as $kataJawab) {

                // 1. LIKE detection (mirip SQL LIKE '%kata%' )
                if (str_contains($kataJawab, $kataKunci) || str_contains($kataKunci, $kataJawab)) {
                    $matchCount++;
                    continue 2;
                }

                // 2. Levenshtein typo tolerance
                $distance = levenshtein($kataKunci, $kataJawab);

                // toleransi typo berdasarkan panjang kata
                $maksTypo = strlen($kataKunci) >= 6 ? 3 : 2;

                if ($distance <= $maksTypo) {
                    $matchCount++;
                    continue 2;
                }

                // 3. Similarity ratio (fallback)
                similar_text($kataKunci, $kataJawab, $percent);

                if ($percent >= 70) { // >= 70% dianggap cocok
                    $matchCount++;
                    continue 2;
                }
            }
        }

        // Rasio kecocokan akhir (0 - 1.0)
        return $matchCount / count($kunciWords);
    }
}
