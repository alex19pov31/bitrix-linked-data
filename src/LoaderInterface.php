<?php

namespace Alex19pov31\LinkedData;

interface LoaderInterface
{
    public function getData(): array;
    public function getItem($key);
}
