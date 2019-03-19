<?php

namespace Alex19pov31\LinkedData\Loaders;

use Alex19pov31\LinkedData\LoaderInterface;
use Alex19pov31\LinkedData\Storage;
use Bitrix\Main\ORM\Data\DataManager;

class HLBlockLoader implements LoaderInterface
{
    use TraitBitrixLoader;

    /**
     * Название таблицы
     *
     * @var string
     */
    private $tableName;

    /**
     * Менеджер данных
     *
     * @var DataManager|null
     */
    private $dataManager;

    public function __construct(string $tableName, $nameRepository = null, $dataManager = null, $storage = null)
    {
        $this->tableName = $tableName;
        $this->nameRepository = $nameRepository;
        $this->dataManager = $dataManager;

        $storageClass = !is_null($storage) ? $storage : Storage::class;
        $storageClass::getRepository($this->repositoryName($tableName))
            ->setLoaderData($this);
    }

    /**
     * Имя репозитория по-умолчанию
     *
     * @param string $tableName
     * @return string
     */
    public static function getRepositoryName(string $tableName): string
    {
        return 'hl_' . $tableName;
    }

    /**
     * Класс для получения данных
     *
     * @return DataManager|null
     */
    private function getDataManager()
    {
        if (!is_null($this->dataManager)) {
            return $this->dataManager;
        }

        return $this->dataManager = getHlBlockClass($this->tableName);
    }

    /**
     * Возвращает данные
     *
     * @return array
     */
    public function getData(): array
    {
        $dataManager = $this->getDataManager();
        if (is_null($dataManager)) {
            return [];
        }

        $data = [];
        $res = $dataManager::getList([
            'filter' => $this->filter,
            'select' => $this->select,
            'order' => $this->sort,
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

    /**
     * Возвращает элемент по ключу
     *
     * @param mixed $key
     * @return mixed
     */
    public function getItem($key)
    {
        $dataManager = $this->getDataManager();
        if (is_null($dataManager)) {
            return [];
        }

        $item = $dataManager::getList([
            'filter' => [$this->getKey() => $key],
        ])->fetch();

        return !empty($item) ? (array) $item : [];
    }
}
