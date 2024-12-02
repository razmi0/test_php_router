<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Model\ProductRepository;
use App\Lib\Routing\Route;
use DI\Attribute\Inject;


class ProductController extends Controller
{
    #[Route(path: '/api/v1.0/produit/list')]
    #[Inject]
    public function list(ProductRepository $repository): mixed
    {
        return json_encode($repository->findAll());
    }

    #[Route('/api/v1.0/produit/listone')]
    #[Inject]
    public function listOne(ProductRepository $repository): mixed
    {
        return json_encode($repository->findOne((string)1));
    }
}
