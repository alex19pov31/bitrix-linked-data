<?php

namespace Alex19pov31\LinkedData\Loaders;

use Alex19pov31\BitrixHelper\IblockHelper;
use Alex19pov31\LinkedData\LoaderInterface;
use Alex19pov31\LinkedData\Storage;

class IblockLoader implements LoaderInterface
{
    use TraitBitrixLoader;

    /**
     * Код инфоблока
     *
     * @var string
     */
    private $iblockCode;

    /**
     * Идентификатор инфоблока
     *
     * @var integer
     */
    private $iblockId;

    /**
     * Менеджер данных
     *
     * @var \CIBlockElement|null
     */
    private $dataManager;

    /**
     * Хелпер инфоблоков
     *
     * @var IblockHelper|null
     */
    private $iblockHelper;

    public function __construct(string $iblockCode, $nameRepository = null, $dataManager = null, $storage = null, $iblockHelper = null)
    {
        $this->iblockCode = $iblockCode;
        $this->iblockHelper = $iblockHelper;
        $this->iblockId = $this->getIblockId($iblockCode);
        $this->nameRepository = $nameRepository;
        $this->dataManager = !is_null($dataManager) ? $dataManager : \CIBlockElement::class;

        $storageClass = !is_null($storage) ? $storage : Storage::class;
        $storageClass::getRepository($this->repositoryName($iblockCode))
            ->setLoaderData($this);
    }

    private function getIblockId(string $iblockCode): int
    {
        if (!is_null($this->iblockHelper)) {
            return (int) $this->iblockHelper::getIblockID($iblockCode);
        }

        IblockHelper::setCacheTime(60);
        $this->iblockHelper = new IblockHelper;
        return (int) $this->iblockHelper::getIblockID($iblockCode);
    }

    /**
     * Имя репозитория по-умолчанию
     *
     * @param string $iblockCode
     * @return string
     */
    public static function getRepositoryName(string $iblockCode): string
    {
        return 'iblock_' . $iblockCode;
    }

    /**
     * Класс для получения данных
     *
     * @return \CIBlockElement
     */
    private function getDataManager()
    {
        return $this->dataManager;
    }

    /**
     * Возвращает данные
     *
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        $filter = $this->filter;
        $filter['IBLOCK_ID'] = $this->iblockId;
        $res = $this->getDataManager()::GetList(
            $this->sort,
            $filter,
            false,
            false,
            $this->select
        );

        while ($item = $res->Fetch()) {
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
        $filter = $this->filter;
        $filter[$this->getKey()] = $key;
        $filter['IBLOCK_ID'] = $this->iblockId;
        $data = $this->getDataManager()::GetList(
            $this->sort,
            $filter,
            false,
            false,
            $this->select
        )->Fetch();

        return !empty($data) ? (array) $data : [];
    }
}
