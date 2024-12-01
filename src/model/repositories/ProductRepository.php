<?php


namespace App\Model;

use App\Lib\Repository;
use App\Model\Entity\Product;
use App\Lib\HTTP\Error;
use Exception;

class ProductRepository extends Repository
{

    /**
     * @return Product[]
     */
    public function findAll()
    {
        try {
            $query = "SELECT * FROM T_PRODUIT ORDER BY date_creation DESC LIMIT 10";
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute())
                Error::HTTP500("Erreur interne");
            $products_from_db = $prepared->fetchAll();
            if (count($products_from_db) == 0)
                Error::HTTP404("Aucun produit trouvé");
            return array_map(fn($product) => Product::make($product), $products_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }
    public function findOne(string $id): Product
    {
        try {
            $query = "SELECT * FROM T_PRODUIT WHERE id = :id";
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute(["id" => $id]))
                Error::HTTP500("Erreur interne");
            $product_from_db = $prepared->fetch();
            if (!$product_from_db)
                Error::HTTP404("Produit non trouvé");
            return Product::make($product_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }
}
