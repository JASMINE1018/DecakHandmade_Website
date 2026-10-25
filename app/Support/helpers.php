<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $amount): string
    {
        return 'Rp '.number_format((int) $amount, 0, ',', '.');
    }
}

if (! function_exists('wib')) {
    function wib($date, string $format = 'd M Y H:i'): string
    {
        if (! $date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->timezone('Asia/Jakarta')->format($format);
    }
}
