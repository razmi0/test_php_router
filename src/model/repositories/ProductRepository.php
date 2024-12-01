<?php

namespace App\Model;

use App\Lib\Repository;
use App\Model\Entity\Product;
use App\Lib\HTTP\Error;
use Exception;

/**
 * @phpstan-import-type ProductType from Product
 */
class ProductRepository extends Repository
{

    /**
     * @return Product[] | void
     */
    public function findAll()
    {
        try {
            $query = "SELECT * FROM T_PRODUIT ORDER BY date_creation DESC LIMIT 10";
            if (!$this->pdo) {
                Error::HTTP500("Internal server error");
                return;
            };
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute()) {
                Error::HTTP500("Internal server error");
                return;
            };
            /**
             * @var ProductType[] | false $products_from_db
             */
            $products_from_db = $prepared->fetchAll();
            if ($products_from_db === false || count($products_from_db) === 0) {
                Error::HTTP404("No products found");
                return;
            };
            return array_map(fn($product) => Product::make($product), $products_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }

    /**
     * @return Product | void
     */
    public function findOne(string $id)
    {
        try {
            $query = "SELECT * FROM T_PRODUIT WHERE id = :id";
            if (!$this->pdo) {
                Error::HTTP500("Internal server error");
                return;
            }
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute(["id" => $id])) {
                Error::HTTP500("Internal server error");
                return;
            }
            /**
             * @var ProductType | false $product_from_db
             */
            $product_from_db = $prepared->fetch();
            if (!$product_from_db) {
                Error::HTTP404("Product not found");
                return;
            }
            return Product::make($product_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }
}
