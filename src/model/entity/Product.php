<?php

namespace App\Model\Entity;

/**
 * Class Product
 * - **getId**
 * - **getName**
 * - **getDescription**
 * - **getPrix**
 * - **getDateCreation**
 * - **setId**
 * - **setName**
 * - **setDescription**
 * - **setPrix**
 * - **setDateCreation**
 * - **toArray** : convert the object to an array
 * 
 * @phpstan-type ProductType=array{ id: string | null, name: string | null, description: string | null, prix: float | null, date_creation: string | null }
 */
class Product
{

    public function __construct(
        private ?string $id = null,
        private ?string $name = null,
        private ?string $description = null,
        private ?float $prix = null,
        private ?string $date_creation = null
    ) {}

    /**
     * @param ProductType $data
     */
    public static function make(array $data): Product
    {
        return new Product(
            $data["id"] ?? null,
            $data["name"] ?? null,
            $data["description"] ?? null,
            $data["prix"] ?? null,
            $data["date_creation"] ?? date("Y-m-d H:i:s"),
        );
    }

    /**
     * @param ProductType[] $data
     * @return Product[]
     */
    public static function makeBulk(array $data): array
    {
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product(
                $product["id"] ?? null,
                $product["name"] ?? null,
                $product["description"] ?? null,
                $product["prix"] ?? null,
                $product["date_creation"] ?? date("Y-m-d H:i:s"),
            );
        }
        return $products;
    }

    /**
     * @return ProductType
     */
    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "prix" => $this->prix,
            "date_creation" => $this->date_creation
        ];
    }

    /**
     * @param Product[] $products
     * @return ProductType[]
     */
    public static function toArrayBulk($products)
    {
        $products_array = [];
        foreach ($products as $product) {
            $products_array[] = $product->toArray();
        }
        return $products_array;
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }


    public function getProductName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getDateCreation(): ?string
    {
        return $this->date_creation;
    }

    public function setDateCreation(string $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }
}
