<?php

namespace App\Services;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class WHOZScoreCalculator
{
    /**
     * Data WHO LMS untuk anak laki-laki (tinggi badan untuk usia)
     * Diambil dari file CSV yang diberikan
     */
    private static function getWHOData(): array
    {
        // Data ini bisa dimuat dari database atau file CSV
        // Untuk contoh ini, saya akan menggunakan beberapa data sampel
        // Dalam implementasi nyata, Anda bisa membaca dari file CSV atau database
        return [
            ['day' => 0, 'L' => 1, 'M' => 49.8842, 'S' => 0.03795],
            ['day' => 1, 'L' => 1, 'M' => 50.0601, 'S' => 0.03785],
            ['day' => 2, 'L' => 1, 'M' => 50.2359, 'S' => 0.03775],
            // ... tambahkan semua data dari CSV atau baca dari database
            ['day' => 1856, 'L' => 1, 'M' => 110.4969, 'S' => 0.04226],
        ];
    }

    /**
     * Melakukan interpolasi linier untuk mendapatkan L, M, S berdasarkan usia dalam hari
     *
     * @param int $usiaHari Usia dalam hari
     * @param array $data Data WHO LMS
     * @return array [L, M, S]
     */
    public static function interpolasi(int $usiaHari, array $data = null): array
    {
        if ($data === null) {
            $data = self::getWHOData();
        }

        $collection = collect($data)->sortBy('day');

        $minDay = $collection->min('day');
        $maxDay = $collection->max('day');

        // Jika usia terlalu muda
        if ($usiaHari < $minDay) {
            $firstRecord = $collection->first();
            return [$firstRecord['L'], $firstRecord['M'], $firstRecord['S']];
        }

        // Jika usia melebihi data referensi
        if ($usiaHari > $maxDay) {
            $lastRecord = $collection->last();
            return [$lastRecord['L'], $lastRecord['M'], $lastRecord['S']];
        }

        // Cari data di bawah dan di atas usia yang dicari
        $dataBawah = $collection->where('day', '<=', $usiaHari)->last();
        $dataAtas = $collection->where('day', '>=', $usiaHari)->first();

        // Jika tepat sama
        if ($dataBawah['day'] == $dataAtas['day']) {
            return [$dataBawah['L'], $dataBawah['M'], $dataBawah['S']];
        }

        // Interpolasi linier
        $proporsi = ($usiaHari - $dataBawah['day']) / ($dataAtas['day'] - $dataBawah['day']);

        $L = $dataBawah['L'] + $proporsi * ($dataAtas['L'] - $dataBawah['L']);
        $M = $dataBawah['M'] + $proporsi * ($dataAtas['M'] - $dataBawah['M']);
        $S = $dataBawah['S'] + $proporsi * ($dataAtas['S'] - $dataBawah['S']);

        return [$L, $M, $S];
    }

    /**
     * Menghitung Z-score berdasarkan rumus WHO LMS
     *
     * @param float $tinggi Tinggi badan dalam cm
     * @param float $L Parameter L
     * @param float $M Parameter M (median)
     * @param float $S Parameter S (koefisien variasi)
     * @return float Z-score
     * @throws InvalidArgumentException
     */
    public static function hitungZScore(float $tinggi, float $L, float $M, float $S): float
    {
        try {
            if ($tinggi <= 0) {
                throw new InvalidArgumentException("Tinggi badan harus lebih besar dari 0");
            }

            if ($M <= 0) {
                throw new InvalidArgumentException("Parameter M harus lebih besar dari 0");
            }

            if ($S <= 0) {
                throw new InvalidArgumentException("Parameter S harus lebih besar dari 0");
            }

            // Jika L mendekati nol (< 1e-7)
            if (abs($L) < 1e-7) {
                $zScore = log($tinggi / $M) / $S;
            } else {
                $zScore = (pow($tinggi / $M, $L) - 1) / ($L * $S);
            }

            return $zScore;
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Error menghitung Z-score: " . $e->getMessage() . ". Periksa nilai tinggi yang dimasukkan.");
        }
    }


    public static function hitungZScoreUntukUsia(float $tinggi, int $usiaHari, array $data): array
    {
        try {
            // Interpolasi untuk mendapatkan L, M, S
            [$L, $M, $S] = self::interpolasi($usiaHari, $data);

            // Hitung Z-score
            $zScore = self::hitungZScore($tinggi, $L, $M, $S);

            // Tentukan status gizi berdasarkan Z-score
            $status = self::tentukanStatusGizi($zScore);

            $result = $zScore < -3 ? -4 : ($zScore > 3 ? 4 : $zScore);

            return [
                'z_score' => (float) round($result, 2),
                'status' => $status,
                'age_months' => floor($usiaHari / 30)
            ];
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Error dalam perhitungan: " . $e->getMessage());
        }
    }

    /**
     * Menentukan status gizi berdasarkan Z-score
     *
     * @param float $zScore
     * @return string
     */
    public static function tentukanStatusGizi(float $zScore): string
    {
        if ($zScore < -3) {
            return 'Sangat Pendek (Severely Stunted)';
        } elseif ($zScore < -2) {
            return 'Pendek (Stunted)';
        } elseif ($zScore <= 2) {
            return 'Normal';
        } else {
            return 'Tinggi';
        }
    }

    /**
     * Load data WHO dari file CSV
     *
     * @param string $csvPath Path ke file CSV
     * @return array
     */
    public static function loadDataFromCSV(string $csvPath): array
    {
        if (!file_exists($csvPath)) {
            throw new InvalidArgumentException("File CSV tidak ditemukan: {$csvPath}");
        }

        $data = [];
        $handle = fopen($csvPath, 'r');

        // Skip header
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $data[] = [
                'day' => (int) $row[0],
                'L' => (float) $row[1],
                'M' => (float) $row[2],
                'S' => (float) $row[3]
            ];
        }

        fclose($handle);
        return $data;
    }
}
