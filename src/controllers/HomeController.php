<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Routing\Route;

class HomeController extends Controller
{
    #[Route(path: "/", view: "/home.php")]
    public function home(): void {}
}
