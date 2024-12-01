<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Injector\Inject;
use App\Lib\Routing\Route;


class HomeController extends Controller
{
    private int $counter_start = 0;

    #[
        Inject(target: "home-controller"),
        Route(path: '/home', view: "/home.php")
    ]
    public function home(): string
    {
        return (
            <<<HTML
                <button id='counter'>Counter : 
                    <span id='counter-output'>$this->counter_start</span>
                </button>
                <script type='module'>
                    import { counter } from '/counter.js';
                    const counterOutput = document.getElementById('counter-output');
                    const counterButton = document.getElementById('counter');
                    counterButton.addEventListener('click', ()=> counter(counterOutput));
                </script>
            HTML
        );
    }
}
