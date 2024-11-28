<?php
function mark(int $precision = 10): float
{
    return round(microtime(true), $precision);
}

function measure(float $pastStamp): float
{
    return (mark() - $pastStamp) * 1000;
}

function print_that(string $location, string $message)
{
    // print_r("[$location] : $message" . PHP_EOL);
}
