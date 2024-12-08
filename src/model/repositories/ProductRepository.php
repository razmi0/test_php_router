<?php

namespace App\Model\Repository;

use App\Lib\Repository;
use App\Model\Entity\Product;
use App\Lib\HTTP\Error;

/**
 * @phpstan-import-type ProductType from Product
 */
class ProductRepository extends Repository
{

    /**
     * @param ?int $limit
     * @return Product[]
     */
    public function findAll(int|null $limit = null): array
    {
        /** @var Product[] */
        $products = [];
        try {
            $query = "SELECT * FROM T_PRODUIT ORDER BY date_creation DESC";
            $query .= $limit ? " LIMIT $limit" : "";
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
        return $product;
    }

    /**
     * @param Product $produit
     */
    public function create(Product $produit): string|false
    {
        try {
            $query = "INSERT INTO T_PRODUIT (name, description, prix, date_creation) VALUES (:name, :description, :prix, :date_creation)";
            /**
             * @var \PDOStatement $prepared
             */
            $prepared = $this->pdo->prepare($query);

            // @phpstan-ignore-next-line
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            $name = $produit->getProductName();
            $description = $produit->getDescription();
            $prix = $produit->getPrix();
            $date_creation = $produit->getDateCreation();

            $prepared->bindParam(':name', $name);
            $prepared->bindParam(':description', $description);
            $prepared->bindValue(':prix', $prix);
            $prepared->bindParam(':date_creation', $date_creation);

            if (!$prepared->execute()) {
                Error::HTTP500("Erreur interne");
            }

            return $this->pdo->lastInsertId();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }

    /**
     * @description Delete a product by its id
     * @param int $id
     * @throws \Exception
     * @return int
     */
    public function delete(int $id): int
    {
        try {
            $query = "DELETE FROM T_PRODUIT WHERE id = :id";
            /**
             * @var \PDOStatement $prepared
             */
            $prepared = $this->pdo->prepare($query);

            // @phpstan-ignore-next-line
            if (!$prepared)
                Error::HTTP500("Erreur interne");


            $prepared->bindParam(':id', $id, \PDO::PARAM_INT);

            if (!$prepared->execute())
                Error::HTTP500("Erreur interne");


            return $prepared->rowCount();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }

    /**
     * @param Product $produit
     * @throws \Exception
     * @return int
     */
    public function update(Product $produit): int
    {
        try {
            $id = $produit->getId();
            $name = $produit->getProductName();
            $description = $produit->getDescription();
            $prix = $produit->getPrix();

            $query = "UPDATE T_PRODUIT SET ";
            if (!empty($name))
                $query .= "name = :name, ";
            if (!empty($description))
                $query .= "description = :description, ";
            if (!empty($prix))
                $query .= "prix = :prix ";

            $query = rtrim($query, ", ");
            $query .= " WHERE id = :id";

            /**
             * @var \PDOStatement $prepared
             */
            $prepared = $this->pdo->prepare($query);

            // @phpstan-ignore-next-line
            if (!$prepared) {
                Error::HTTP500("Erreur interne", ["id" => $id]);
            }

            $prepared->bindParam(':id', $id, \PDO::PARAM_INT);
            if (!empty($name))
                $prepared->bindParam(':name', $name);
            if (!empty($description))
                $prepared->bindParam(':description', $description);
            if (!empty($prix))
                $prepared->bindValue(':prix', $prix);

            if (!$prepared->execute()) {
                Error::HTTP500("Erreur interne", ["id" => $id]);
            }

            return $prepared->rowCount();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->connection->close();
        }
    }
}
