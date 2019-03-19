<?php

namespace Alex19pov31\LinkedData\Loaders;

trait TraitBitrixLoader
{
    /**
     * Список полей для выборки
     *
     * @var array
     */
    protected $select = [];

    /**
     * Порядок сортировки данных
     *
     * @var array
     */
    protected $sort = [];

    /**
     * Правила фильтрации данных
     *
     * @var array
     */
    protected $filter = [];

    /**
     * Имя репозитория
     *
     * @var string
     */
    protected $nameRepository;

    /**
     * Название поля используемое как ключ
     *
     * @var string
     */
    protected $key = 'ID';

    /**
     * Выборка полей
     *
     * @param array $select
     * @return TraitBitrixLoader
     */
    public function select(array $select): self
    {
        $this->select = $select;
        return $this;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    private function getKey(): string
    {
        return (string) $this->key;
    }

    /**
     * Сортировка данных
     * ["field"=>"asc|desc"]
     *
     * @param array $sort
     * @return TraitBitrixLoader
     */
    public function sort(array $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Фильтрация данных
     *
     * @param array $filter
     * @return TraitBitrixLoader
     */
    public function filter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    private function repositoryName(string $key): string
    {
        if (!empty($this->nameRepository)) {
            return (string) $this->nameRepository;
        }

        return static::getRepositoryName($key);
    }

    abstract public static function getRepositoryName(string $key): string;
}
