<?php

namespace Alex19pov31\LinkedData;

class BaseRepository implements RepositoryInterface
{
    /**
     * Данные
     *
     * @var array|null
     */
    protected $data;

    /**
     * Время кеширования
     *
     * @var integer
     */
    private $ttl = 0;

    /**
     * Название репозитория
     *
     * @var string
     */
    protected $name;

    /**
     * Загрузчик данных
     *
     * @var LoaderInterface|null
     */
    private $loaderData;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Название репозитория
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Установить кеш
     *
     * @param integer $minutes
     * @return RepositoryInterface
     */
    public function cache(int $minutes): RepositoryInterface
    {
        $this->ttl = $minutes;
        return $this;
    }

    /**
     * Обновить кеш
     *
     * @param integer $minutes
     * @return RepositoryInterface
     */
    public function updateCache(int $minutes): RepositoryInterface
    {
        setCacheData($minutes, 'repository_' . $this->getName(), '/repositories', 'cache', $this->data);
        return $this;
    }

    /**
     * Инициализация хранилища
     *
     * @return RepositoryInterface
     */
    public function init(): RepositoryInterface
    {
        if ($this->isEmpty() && !is_null($this->loaderData)) {
            if ($this->ttl == 0) {
                $this->putData((array) $this->initWithoutCache());
                return $this;
            }

            $data = cache($this->ttl, 'repository_' . $this->getName(), '/repositories', 'cache', function () {
                return $this->putData((array) $this->initWithoutCache());
            });

            $this->putData((array) $data);
        }

        return $this;
    }

    private function initWithoutCache(): array
    {
        return (array) $this->loaderData->getData();
    }

    /**
     * Возвращает все данные из хранилища
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Вернуть элемент по ключу
     *
     * @param mixed $key
     * @return mixed
     */
    public function getItemByKey($key)
    {
        $this->init();

        if (!isset($this->data[$key])) {
            if (!is_null($this->loaderData)) {
                $data = $this->loaderData->getItem($key);
                if (!empty($data)) {
                    $this->addItem($key, $data);
                    return $data;
                }
            }

            return null;
        }

        return $this->data[$key];
    }

    /**
     * Добавить элемент в хранилище
     *
     * @param mixed $key
     * @param mixed $data
     * @return void
     */
    public function addItem($key, $data)
    {
        $this->data[$key] = $data;
    }

    /**
     * Загрузить данные в хранилище
     *
     * @param array $data
     * @return void
     */
    public function putData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->addItem($key, $value);
        }
    }

    /**
     * Проверка наличия данных в хранилище
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Количество элементов в хранилище
     *
     * @return integer
     */
    public function getCount(): int
    {
        return count($this->data);
    }

    /**
     * Регистрация загрузчика данных
     *
     * @param LoaderInterface $loader
     * @return void
     */
    public function setLoaderData(LoaderInterface $loader)
    {
        $this->loaderData = $loader;
    }
}
