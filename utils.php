<?php
function mark(int $precision = 10): float
{
    return round(microtime(true), $precision);
}

function measure(float $pastStamp): float
{
    return (mark() - $pastStamp) * 1000;
}
