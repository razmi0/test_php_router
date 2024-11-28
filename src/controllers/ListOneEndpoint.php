<?php

namespace App\Controllers;

use App\Model\Data;
use App\Router\Route;



#[Route('/api/v1.0/produit/listone')]
class ListOneEndpoint
{
    public function __construct(
        private int $id,
    ) {
        print_r("hi from ListOneEndpoint");
    }

    public function get()
    {
        $data = new Data();
        $item = $data->getId($this->id);
        if ($item === false) {
            return json_encode(['error' => 'Item not found']);
        }
        return json_encode($item);
    }

    public function __invoke()
    {
        echo $this->get();
    }
}
