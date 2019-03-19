<?php

namespace Alex19pov31\LinkedData\Loaders;

use Alex19pov31\LinkedData\LoaderInterface;
use Alex19pov31\LinkedData\Storage;

class SimpleLoader implements LoaderInterface
{
    /**
     * Функция для получения данных
     *
     * @var callable|null
     */
    private $dataLoadCallback;

    /**
     * Функция для получения элемента
     *
     * @var callable|null
     */
    private $itemLoadCallback;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $repositoryName;

    public function __construct(string $repositoryName, $storage = null)
    {
        $this->repositoryName = $repositoryName;
        $storageClass = !is_null($storage) ? $storage : Storage::class;
        $storageClass::getRepository($this->repositoryName)
            ->setLoaderData($this);
    }

    /**
     * Загрузчик данных
     *
     * @param callable $dataLoadCallback
     * @return SimpleLoader
     */
    public function setDataLoadCallback(callable $dataLoadCallback): SimpleLoader
    {
        $this->dataLoadCallback = $dataLoadCallback;
        return $this;
    }

    /**
     * Загрузчик элемента
     *
     * @param callable $itemLoadCallback
     * @return SimpleLoader
     */
    public function setItemLoadCallback(callable $itemLoadCallback): SimpleLoader
    {
        $this->itemLoadCallback = $itemLoadCallback;
        return $this;
    }

    /**
     * Возвращает данные
     *
     * @return array
     */
    public function getData(): array
    {
        if (is_null($this->dataLoadCallback)) {
            return [];
        }

        $func = $this->dataLoadCallback;
        return (array) $func();
    }

    /**
     * Возвращает элемент по ключу
     *
     * @param mixed $key
     * @return mixed
     */
    public function getItem($key)
    {
        if (is_null($this->itemLoadCallback)) {
            return [];
        }

        $func = $this->itemLoadCallback;
        return (array) $func($key);
    }
}
