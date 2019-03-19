<?php

namespace Alex19pov31\LinkedData;

interface StorgeInterface
{
    /**
     * Возвращает репозиторий
     *
     * @param string $name
     * @return RepositoryInterface|null
     */
    public static function getRepository(string $name);
}
