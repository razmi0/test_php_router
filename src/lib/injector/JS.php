<?php


namespace App\Lib\Injector;

class JS
{
    public static function write(string $js): string
    {
        return trim("
        <script type='module'>
        (() => { 
        $js 
        })();
        </script>
        ");
    }
}
