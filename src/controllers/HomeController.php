<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Injector\Inject;
use App\Lib\Routing\Route;


class HomeController extends Controller
{
    private int $counter_start = 0;

    #[Route(path: '/home', view: "/home.php"), Inject(target: "home-controller")]
    public function home(): string
    {

        $counter = self::createJsClosure(
            <<<JS
            const counter_output = document.querySelector('#counter-output');
            counter_output.textContent = parseInt(counter_output.textContent) + 1;
        JS
        );

        return (
            <<<HTML
                <button id='counter'>Counter : 
                    <span id='counter-output'>$this->counter_start</span>
                </button>
                <script>
                    document.querySelector('#counter').addEventListener('click', $counter);
                </script>
            HTML
        );
    }
}
