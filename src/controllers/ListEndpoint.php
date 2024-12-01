<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Model\ProductRepository;
use App\Lib\Routing\Route;


#[Route(path: '/api/v1.0/produit/list')]
class ListEndpoint  extends Controller
{
    private $products;
    public function handle(): void
    {
        $repository = new ProductRepository();
        $this->products = $repository->findAll();
        print_r($this->products);
    }
}
