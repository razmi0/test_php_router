<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\Injector\ContentInjector;
use App\Lib\Injector\JS;
use App\Lib\Routing\Route;


class CounterController extends Controller
{
    private int $counter_start = 0; // not dynamic, js starter value

    #[Route(path: '/counter', view: "/counter.php"), ContentInjector(target: "counter-controller")]
    public function home(): string
    {

        $counter = JS::write(
            <<<JS
            const button = document.querySelector('#counter');
            const counter_output = document.querySelector('#counter-output');
            let interval = null;
            const count = () => {
                counter_output.dataset.value = parseInt(counter_output.dataset.value) + 1;
                counter_output.textContent = "Counting " + counter_output.dataset.value;
            }
            const clock = () => {
                if(!interval) {
                    interval = setInterval(count, 1000);
                    return;
                } 
                clearInterval(interval);
                counter_output.textContent = "Stopped at " + counter_output.dataset.value;
                interval = null;
            }
            button.addEventListener('click', );
        JS
        );

        return
            <<<HTML
            <button id='counter' hx>
                    <span id='counter-output' data-value='$this->counter_start'>0</span>
                </button>
            $counter
            HTML;
    }
}
