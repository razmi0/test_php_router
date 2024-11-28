<?php

namespace App\Controllers;

abstract class AbstractController
{
    public function handle()
    {
        print_r("hi from Controller");
    }
}
