<?php

namespace Alex19pov31\Tests\LinkedData\Loaders;

use Alex19pov31\LinkedData\Loaders\IblockLoader;
use PHPUnit\Framework\TestCase;

class IblockLoaderTest extends TestCase
{
    private static $dataSet;
    private static $dataManager;
    private static $iblockHelper;

    public function testGetRepositoryName()
    {
        $storage = \Mockery::spy('Alex19pov31\LinkedData\Storage');
        $storage->shouldReceive('getRepository')->andReturnSelf();
        $storage->shouldReceive('setLoaderData')->andReturnTrue();

        $loader = new IblockLoader('testing', null, static::$dataManager, $storage, static::$iblockHelper);
        $repositoryName = $loader::getRepositoryName('testing');
        $this->assertEquals($repositoryName, 'iblock_testing');
        $storage->shouldHaveReceived()->getRepository('iblock_testing')->once();
        $storage->shouldHaveReceived()->setLoaderData($loader)->once();

        $loader = new IblockLoader('testing', 'alt_name', static::$dataManager, $storage, static::$iblockHelper);
        $storage->shouldHaveReceived()->getRepository('alt_name')->once();
        $storage->shouldHaveReceived()->setLoaderData($loader)->once();
    }

    public function testGetData()
    {
        $loader = new IblockLoader('testing', null, static::$dataManager, null, static::$iblockHelper);
        $loader->select(['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_TEST']);
        $loader->sort(['ID' => 'ASC']);
        $loader->filter(['>ID' => 0]);

        $this->assertEquals($loader->getData(), [
            1 => static::$dataSet[0],
            35 => static::$dataSet[1],
            21 => static::$dataSet[2],
        ]);

        static::$dataManager->shouldHaveReceived()->GetList(
            ['ID' => 'ASC'],
            ['>ID' => 0, 'IBLOCK_ID' => 2],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_TEST']
        );
        static::$dataManager->shouldHaveReceived('Fetch')->times(4);
    }

    public function testGetDataByKey()
    {
        $loader = new IblockLoader('testing', null, static::$dataManager, null, static::$iblockHelper);
        $loader->select(['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_TEST']);
        $loader->sort(['ID' => 'ASC']);
        $loader->filter(['>ID' => 0]);

        $this->assertEquals($loader->setKey('PROPERTY_TEST_VALUE')->getData(), [
            'one' => static::$dataSet[0],
            'two' => static::$dataSet[1],
            'three' => static::$dataSet[2],
        ]);

        static::$dataManager->shouldHaveReceived()->GetList(
            ['ID' => 'ASC'],
            ['>ID' => 0, 'IBLOCK_ID' => 2],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_TEST']
        );
        static::$dataManager->shouldHaveReceived('Fetch')->times(4);
    }

    public function testGetItem()
    {
        $loader = new IblockLoader('testing', null, static::$dataManager, null, static::$iblockHelper);
        $this->assertEquals($loader->getItem(1), static::$dataSet[0]);
        static::$dataManager->shouldHaveReceived()->GetList(
            [],
            ['ID' => 1, 'IBLOCK_ID' => 2],
            false,
            false,
            []
        );
        static::$dataManager->shouldHaveReceived('Fetch')->once();
    }

    public function testGetItemByKey()
    {
        $loader = new IblockLoader('testing', null, static::$dataManager, null, static::$iblockHelper);
        $loader->setKey('PROPERTY_TEST_VALUE');
        $this->assertEquals($loader->getItem('one'), static::$dataSet[0]);
        static::$dataManager->shouldHaveReceived()->GetList(
            [],
            ['PROPERTY_TEST_VALUE' => 'one', 'IBLOCK_ID' => 2],
            false,
            false,
            []
        );
        static::$dataManager->shouldHaveReceived('Fetch')->once();
    }

    protected function setUp()
    {
        static::$dataSet = [
            [
                'ID' => 1,
                'IBLOCK_ID' => 2,
                'CODE' => 'testing',
                'NAME' => 'Тестирование 1',
                'PROPERTY_TEST_VALUE' => 'one',
            ],
            [
                'ID' => 35,
                'IBLOCK_ID' => 2,
                'CODE' => 'testing',
                'NAME' => 'Тестирование 2',
                'PROPERTY_TEST_VALUE' => 'two',
            ],
            [
                'ID' => 21,
                'IBLOCK_ID' => 2,
                'CODE' => 'testing',
                'NAME' => 'Тестирование 3',
                'PROPERTY_TEST_VALUE' => 'three',
            ],
        ];

        $dataManager = \Mockery::spy('CIBlockElement');
        $dataManager->shouldReceive('GetList')->andReturnSelf();
        $dataManager->shouldReceive('Fetch')->andReturn(
            static::$dataSet[0],
            static::$dataSet[1],
            static::$dataSet[2],
            null,
        );

        static::$dataManager = $dataManager;

        static::$iblockHelper = \Mockery::spy('Alex19pov31\BitrixHelper\IblockHelper');
        static::$iblockHelper->shouldReceive('getIblockID')->andReturn(2);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }
}
