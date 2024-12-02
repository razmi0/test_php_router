<?php

namespace App\Model\Repository;

use App\Lib\Repository;
use App\Model\Entity\Product;
use App\Lib\HTTP\Error;
use Closure;
use Exception;

/**
 * @phpstan-import-type ProductType from Product
 */
class ProductRepository extends Repository
{

    /**
     * @return Product[]
     */
    public function findAll()
    {
        /** @var Product[] */
        $products = [];
        try {
            $query = "SELECT * FROM T_PRODUIT ORDER BY date_creation DESC LIMIT 10";
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute()) {
                Error::HTTP500("Internal server error");
            };
            /**
             * @var ProductType[] | false $products_from_db
             */
            $products_from_db = $prepared->fetchAll();
            if ($products_from_db === false || count($products_from_db) === 0) {
                Error::HTTP404("No products found");
            };
            // @phpstan-ignore argument.type
            $products = Product::makeBulk($products_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
        return $products;
    }



    /**
     * @return Product | null
     */
    public function findOne(string $id)
    {
        $product = null;
        try {
            $query = "SELECT * FROM T_PRODUIT WHERE id = :id";
            $prepared = $this->pdo->prepare($query);
            if (!$prepared || !$prepared->execute(["id" => $id])) {
                Error::HTTP500("Internal server error");
            }
            /**
             * @var ProductType | false $product_from_db
             */
            $product_from_db = $prepared->fetch();
            if ($product_from_db === false) {
                Error::HTTP404("Product not found");
            }
            // @phpstan-ignore argument.type
            $product = Product::make($product_from_db);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
        return $product;
    }
}
