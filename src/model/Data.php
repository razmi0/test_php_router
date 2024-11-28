<?php


namespace App\Model;


require_once BASE_DIR . '/vendor/autoload.php';


class Data
{
    public const LIST_MAX_SIZE = 100;
    private array $list = [];

    public function __construct()
    {

        for ($i = 0; $i < self::LIST_MAX_SIZE; $i++) {
            $list[] = [
                'id' => $i,
                'name' => "Item $i",
                'description' => "Description for item $i",
            ];
        }

        $this->list = $list;
    }

    public function get(int $limit = self::LIST_MAX_SIZE, string $sort = 'ASC'): array
    {
        $list = array_slice($this->list, 0, $limit);

        if ($sort === 'DESC')
            $list = array_reverse($list);

        return $list;
    }

    public function getId(int $id): array|false
    {
        return array_search($id, array_column($this->list, 'id'));
    }
}
