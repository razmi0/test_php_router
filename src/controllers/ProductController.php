<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Model\ProductRepository;
use App\Lib\Routing\Route;


class ProductController extends Controller
{
    #[Route(path: '/api/v1.0/produit/list')]
    public function list(ProductRepository $repository): mixed
    {
        return json_encode($repository->findAll());
    }

    #[Route('/api/v1.0/produit/listone')]
    public function listOne(ProductRepository $repository): mixed
    {
        return json_encode($repository->findOne((string)1));
    }
}
