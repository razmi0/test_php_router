<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Model\Repository\ProductRepository;
use App\Lib\Routing\Route;
use App\Model\Entity\Product;

class ProductController extends Controller
{
    #[Route(path: '/api/v1.0/produit/list')]
    public function list(ProductRepository $repository): mixed
    {
        header('Content-Type: application/json');
        $products = $repository->findAll();
        if (!$products) {
            return json_encode([]);
        }
        return json_encode(Product::toArrayBulk($products));
    }

    #[Route('/api/v1.0/produit/listone')]
    public function listOne(ProductRepository $repository): mixed
    {
        header('Content-Type: application/json');
        $product = $repository->findOne("1");
        if (!$product) {
            return json_encode([]);
        }
        return json_encode($product->toArray());
    }
}
