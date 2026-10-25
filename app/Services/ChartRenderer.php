<?php

namespace App\Services;

class ChartRenderer
{
    /**
     * Render grafik garis sederhana sebagai PNG.
     *
     * @param  array<int, array{date:string, revenue:int}>  $points
     */
    public function lineChart(array $points, int $width = 900, int $height = 280): string
    {
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        $grid = imagecolorallocate($image, 224, 221, 216);
        $axis = imagecolorallocate($image, 169, 158, 150);
        $line = imagecolorallocate($image, 200, 109, 81);
        $text = imagecolorallocate($image, 44, 36, 32);

        imagefill($image, 0, 0, $white);

        $padding = 40;
        $chartW = $width - ($padding * 2);
        $chartH = $height - ($padding * 2);

        $max = max(1, collect($points)->max('revenue'));

        for ($i = 0; $i <= 4; $i++) {
            $y = $padding + (int) ($chartH * $i / 4);
            imageline($image, $padding, $y, $width - $padding, $y, $grid);
        }

        imageline($image, $padding, $padding, $padding, $height - $padding, $axis);
        imageline($image, $padding, $height - $padding, $width - $padding, $height - $padding, $axis);

        $count = count($points);
        if ($count > 0) {
            $step = $count > 1 ? $chartW / ($count - 1) : 0;
            $previous = null;

            foreach (array_values($points) as $i => $point) {
                $x = (int) round($padding + ($step * $i));
                $y = (int) round(($height - $padding) - (($point['revenue'] / $max) * $chartH));

                if ($previous) {
                    imageline($image, $previous[0], $previous[1], $x, $y, $line);
                }

                imagefilledellipse($image, $x, $y, 6, 6, $line);
                $previous = [$x, $y];

                if ($count <= 10 || $i % (int) ceil($count / 10) === 0) {
                    $label = substr($point['date'], 5);
                    imagestring($image, 2, $x - 18, $height - $padding + 10, $label, $text);
                }
            }
        }

        imagestring($image, 3, $padding, 12, 'Penjualan harian (Rp)', $text);

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        unset($image);

        return $binary;
    }
}
