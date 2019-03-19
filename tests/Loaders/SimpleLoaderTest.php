<?php

namespace Alex19pov31\Tests\LinkedData\Loaders;

use Alex19pov31\LinkedData\Loaders\SimpleLoader;
use PHPUnit\Framework\TestCase;

class SimpleLoaderTest extends TestCase
{
    public function testSetLoader()
    {
        $storage = \Mockery::spy('Alex19pov31\LinkedData\Storage');
        $storage->shouldReceive('getRepository')->andReturnSelf();
        $storage->shouldReceive('setLoaderData')->andReturnTrue();

        $loader = new SimpleLoader('test_repo', $storage);
        $storage->shouldHaveReceived()->getRepository('test_repo')->once();
        $storage->shouldHaveReceived()->setLoaderData($loader)->once();
        \Mockery::close();
    }

    public function testSetCallbackData()
    {
        $data = [1, 23, 4, 5];
        $loader = new SimpleLoader('test_repo');
        $loader->setDataLoadCallback(function () use ($data) {
            return $data;
        });

        $this->assertEquals($loader->getData(), $data);
    }

    public function testSetCallbackItem()
    {
        $loader = new SimpleLoader('test_repo');
        $loader->setItemLoadCallback(function ($key) {
            if ($key == 3) {
                return [42];
            }

            return [21];
        });

        $this->assertEquals($loader->getItem(3), [42]);
        $this->assertEquals($loader->getItem(35), [21]);
    }
}
