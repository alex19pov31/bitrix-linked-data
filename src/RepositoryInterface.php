<?php

namespace Alex19pov31\LinkedData;

interface RepositoryInterface
{
    public function getName(): string;
    public function getItemByKey($key);
    public function addItem($key, $data);
    public function putData(array $data);
    public function isEmpty(): bool;
    public function init(): RepositoryInterface;
    public function getCount(): int;
}
