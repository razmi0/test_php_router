<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\HTTP\Error;
use App\Model\Repository\ProductRepository;
use App\Lib\Routing\Route;
use App\Model\Entity\Product;

/**
 * Class ProductController
 * 
 * @phpstan-import-type ProductType from Product
 */
class ProductController extends Controller
{
    #[Route('/api/v1.0/produit/list')]
    public function list(ProductRepository $repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["GET"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        $products = Product::toArrayBulk($repository->findAll());

        $this->response
            ->setCode(200)
            ->setMessage("Liste des produits")
            ->setPayload(["products" => $products])
            ->setContentType("application/json")
            ->send();
    }

    #[Route('/api/v1.0/produit/listone')]
    public function listOne(ProductRepository $repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["GET"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        /**
         * @var string|null $id
         */
        $id = $this->request->getHasQuery() ? $this->request->getQueryParam("id") : $this->request->getDecodedData("id");

        if (!$id) {
            Error::HTTP400("ID manquant");
            return;
        }

        $product = $repository->findOne($id);

        if (!$product) {
            Error::HTTP404("Produit non trouvé");
            return;
        }

        $this->response
            ->setCode(200)
            ->setMessage("Produit trouvé")
            ->setPayload(["product" => $product->toArray()])
            ->setContentType("application/json")
            ->send();
    }

    #[Route('/api/v1.0/produit/new')]
    public function create(ProductRepository $repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["POST"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        if (!$this->request->getHasData()) {
            Error::HTTP400("Données manquantes");
            return;
        }

        /**
         * @var ProductType $client_data
         */
        $client_data = $this->request->getDecodedData();

        $inserted_id = $repository->create(Product::make($client_data));

        if ($inserted_id === false) {
            Error::HTTP500("Erreur lors de la création du produit");
            return;
        }

        $this->response
            ->setCode(201)
            ->setMessage("Produit créé")
            ->setPayload(["id" => $inserted_id])
            ->setContentType("application/json")
            ->send();
    }

    #[Route('/api/v1.0/produit/update')]
    public function update(ProductRepository $repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["PUT"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        if (!$this->request->getHasData()) {
            Error::HTTP400("Données manquantes");
            return;
        }

        /**
         * @var ProductType $client_data
         */
        $client_data = $this->request->getDecodedData();

        $updated = $repository->update(Product::make($client_data));

        if (!$updated) {
            Error::HTTP500("Erreur lors de la mise à jour du produit");
            return;
        }

        $this->response
            ->setCode(200)
            ->setMessage("Produit mis à jour")
            ->setPayload(["id" => $client_data["id"]])
            ->setContentType("application/json")
            ->send();
    }

    #[Route('/api/v1.0/produit/delete')]
    public function delete(ProductRepository $repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["DELETE"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        if (!$this->request->getHasData()) {
            Error::HTTP400("Données manquantes");
            return;
        }

        /**
         * @var string $id
         */
        $id = $this->request->getDecodedData("id");

        if (!$id) {
            Error::HTTP400("ID manquant");
            return;
        }

        $deleted = $repository->delete((int)$id);

        if (!$deleted) {
            Error::HTTP500("Erreur lors de la suppression du produit");
            return;
        }

        $this->response
            ->setCode(200)
            ->setMessage("Produit supprimé")
            ->setPayload(["id" => $id])
            ->setContentType("application/json")
            ->send();
    }
}
