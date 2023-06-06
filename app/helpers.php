<?php
    declare(strict_types=1);

    function formatNairaAmount(float $amount): string
    {
        $isNegative = $amount < 0;

        return ($isNegative ? '-' : '') . 'NGN' . number_format(abs($amount), 2);
    }