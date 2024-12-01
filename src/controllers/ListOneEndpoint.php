<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Model\Data;
use App\Lib\Routing\Route;
use App\Model\ProductRepository;

#[Route('/api/v1.0/produit/listone')]
class ListOneEndpoint extends Controller
{

    public function handle(): void
    {
        $repository = new ProductRepository();
        $product = $repository->findOne(1);
    }
}
