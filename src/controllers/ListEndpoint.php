<?php

namespace App\Controllers;

use App\Router\Route;
use App\Model\Data;


#[Route('/api/v1.0/produit/list')]
class ListEndpoint extends AbstractController
{
    public function __construct(
        private int $limit = 10,
        private string $sort = 'asc',
    ) {
        print_r("hi from ListEndpoint constructor");
    }

    public function get()
    {
        $data = new Data();
        $list = $data->get($this->limit, $this->sort);
        return json_encode($list);
    }

    public function handle()
    {
        print_r("hi from ListEndpoint handle" . PHP_EOL);
        print_r($this->get());
        print_r(PHP_EOL);
    }
}
