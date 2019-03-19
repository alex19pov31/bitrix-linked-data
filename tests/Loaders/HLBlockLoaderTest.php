<?php

namespace Alex19pov31\Tests\LinkedData\Loaders;

use Alex19pov31\LinkedData\Loaders\DataEntityLoader;
use Alex19pov31\LinkedData\Loaders\HLBlockLoader;
use PHPUnit\Framework\TestCase;

class HLBlockLoaderTest extends TestCase
{
    private static $dataSet;
    private static $dataManager;

    public function testGetRepositoryName()
    {
        $storage = \Mockery::spy('Alex19pov31\LinkedData\Storage');
        $storage->shouldReceive('getRepository')->andReturnSelf();
        $storage->shouldReceive('setLoaderData')->andReturnTrue();

        $loader = new HLBlockLoader('test', null, static::$dataManager, $storage);

        $tableName = static::$dataManager->getTableName();
        $repositoryName = $loader::getRepositoryName($tableName);

        $this->assertEquals($repositoryName, 'hl_' . $tableName);
        $storage->shouldHaveReceived()->getRepository('hl_' . $tableName)->once();
        $storage->shouldHaveReceived()->setLoaderData($loader)->once();

        $loader = new DataEntityLoader(static::$dataManager, 'alt_name', $storage);
        $storage->shouldHaveReceived()->getRepository('alt_name')->once();
        $storage->shouldHaveReceived()->setLoaderData($loader)->once();
    }

    public function testGetData()
    {
        $loader = new DataEntityLoader(static::$dataManager);
        $loader->select(['ID', 'FIELD1', 'FIELD2', 'FIELD3']);
        $loader->sort(['ID' => 'ASC']);
        $loader->filter(['>ID' => 0]);

        $this->assertEquals($loader->getData(), [
            1 => static::$dataSet[0],
            35 => static::$dataSet[1],
            21 => static::$dataSet[2],
        ]);

        static::$dataManager->shouldHaveReceived()->getList([
            'select' => ['ID', 'FIELD1', 'FIELD2', 'FIELD3'],
            'filter' => ['>ID' => 0],
            'sort' => ['ID' => 'ASC'],
        ]);
        static::$dataManager->shouldHaveReceived('fetch')->times(4);
    }

    public function testGetDataByKey()
    {
        $loader = new DataEntityLoader(static::$dataManager);
        $loader->select(['ID', 'FIELD1', 'FIELD2', 'FIELD3']);
        $loader->sort(['ID' => 'ASC']);
        $loader->filter(['>ID' => 0]);

        $this->assertEquals($loader->setKey('FIELD3')->getData(), [
            'test1' => static::$dataSet[0],
            'test2' => static::$dataSet[1],
            'test3' => static::$dataSet[2],
        ]);

        static::$dataManager->shouldHaveReceived()->getList([
            'select' => ['ID', 'FIELD1', 'FIELD2', 'FIELD3'],
            'filter' => ['>ID' => 0],
            'sort' => ['ID' => 'ASC'],
        ]);
        static::$dataManager->shouldHaveReceived('fetch')->times(4);
    }

    public function testGetItem()
    {
        $loader = new DataEntityLoader(static::$dataManager);
        $this->assertEquals($loader->getItem(1), static::$dataSet[0]);
        static::$dataManager->shouldHaveReceived()->getList([
            'filter' => ['ID' => 1],
        ]);
        static::$dataManager->shouldHaveReceived('fetch')->once();
    }

    public function testGetItemByKey()
    {
        $loader = new DataEntityLoader(static::$dataManager);
        $loader->setKey('FIELD2');

        $this->assertEquals($loader->getItem('testing'), static::$dataSet[0]);
        static::$dataManager->shouldHaveReceived()->getList([
            'filter' => ['FIELD2' => 'testing'],
        ]);
        static::$dataManager->shouldHaveReceived('fetch')->once();
    }

    protected function setUp()
    {
        static::$dataSet = [
            [
                'ID' => 1,
                'FIELD1' => 'Тестовый #1',
                'FIELD2' => 'testing',
                'FIELD3' => 'test1',
            ],
            [
                'ID' => 35,
                'FIELD1' => 'Тестовый #2',
                'FIELD2' => 'testing2',
                'FIELD3' => 'test2',
            ],
            [
                'ID' => 21,
                'FIELD1' => 'Тестовый #3',
                'FIELD2' => 'testing3',
                'FIELD3' => 'test3',
            ],
        ];

        $dataManager = \Mockery::spy('Bitrix\Main\ORM\Data\DataManager');
        $dataManager->shouldReceive('getTableName')->andReturn('test');
        $dataManager->shouldReceive('getById')->andReturnSelf();
        $dataManager->shouldReceive('getList')->andReturnSelf();
        $dataManager->shouldReceive('fetch')->andReturn(
            static::$dataSet[0],
            static::$dataSet[1],
            static::$dataSet[2],
            null
        );
        $dataManager->shouldReceive('fetchAll')->andReturn(static::$dataSet);

        static::$dataManager = $dataManager;
    }

    protected function tearDown()
    {
        \Mockery::close();
    }
}
