<?php

namespace Alex19pov31\LinkedData;

class Storage implements StorgeInterface
{
    /**
     * Список репозиториев
     *
     * @var array|null
     */
    private static $data;

    /**
     * Возвращает репозиторий по названию
     *
     * @param string $name
     * @return RepositoryInterface|null
     */
    public static function getRepository(string $name)
    {
        if (!isset(static::$data[$name])) {
            return static::$data[$name] = new BaseRepository($name);
        }

        return static::$data[$name];
    }

    /**
     * Сохранить данные репозиториев в кеш
     *
     * @param integer $minutes
     * @return void
     */
    public static function saveToCache(int $minutes)
    {
        if (is_null(static::$data)) {
            return;
        }

        foreach (static::$data as $repository) {
            $repository->updateCache($minutes);
        }
    }
}
