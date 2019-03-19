<?php

namespace Alex19pov31\LinkedData\Loaders;

use Alex19pov31\LinkedData\LoaderInterface;
use Alex19pov31\LinkedData\Storage;
use Bitrix\Main\ORM\Data\DataManager;

class DataEntityLoader implements LoaderInterface
{
    use TraitBitrixLoader;

    private $dataManager;

    public function __construct(DataManager $dataManager, $nameRepository = null, $storage = null)
    {
        $this->dataManager = $dataManager;
        $this->nameRepository = $nameRepository;
        $tableName = $this->getDataManager()::getTableName();

        $storageClass = !is_null($storage) ? $storage : Storage::class;
        $storageClass::getRepository($this->repositoryName($tableName))
            ->setLoaderData($this);
    }

    /**
     * Название репозитория
     *
     * @return string
     */
    public static function getRepositoryName(string $tableName): string
    {
        return 'table_' . $tableName;
    }

    /**
     * Класс для получения данных
     *
     * @return DataManager
     */
    private function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    public function getData(): array
    {
        $data = [];
        $res = $this->getDataManager()::getList([
            'select' => $this->select,
            'filter' => $this->filter,
            'sort' => $this->sort,
        ]);

        while ($item = $res->fetch()) {
            if (empty($item[$this->getKey()])) {
                $data[] = $item;
                continue;
            }

            $key = $item[$this->getKey()];
            $data[$key] = $item;
        }

        return $data;
    }

    public function getItem($key)
    {
        $item = $this->getDataManager()::getList([
            'filter' => [$this->getKey() => $key],
        ])->fetch();

        return !empty($item) ? (array) $item : [];
    }
}
