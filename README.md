[![Latest Stable Version](https://poser.pugx.org/alex19pov31/bitrix-linked-data/v/stable)](https://packagist.org/packages/alex19pov31/bitrix-linked-data) [![Build Status](https://travis-ci.org/alex19pov31/bitrix-linked-data.svg?branch=master)](https://travis-ci.org/alex19pov31/bitrix-linked-data) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex19pov31/bitrix-linked-data/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex19pov31/bitrix-linked-data/?branch=master)

# Bitrix linked data

Хелпер для работы с часто используемыми данными - справочниками и т.д. Пердставляет из себя список именованных хранилищ данных. Предназначен для того чтобы избежать повторной выборки данных, вместо этого данные кешируется в статичном параметре класса хранилища и запрашиваются из него же.

## Установка

```bash
composer require alex19pov31/bitrix-linked-data
```

## Работа с хранилищем

```php
use Alex19pov31\LinkedData\Storage;

/**
 * Возвращает хранилище данных
 * 
 *  test_data - имя хранилища
 */
$repository = Storage::getRepository('test_data');

// Зашрузить данные в хранилище
$repository->putData([1,2,4,6]);

// Добавить запись в хранилище с заданных ключом
$repository->addItem('key', 'data');

// Проверка наличия данных в хранилище
$repository->isEmpty();

// Количество записей
$repository->getCount();

// Установить загрузчик данных  (Alex19pov31\LinkedData\LoaderInterface)
$repository->setLoaderData($loader);

// Инициализация данных хроанилища (если если установлен загрузчик)
$repository->init();

// Вернуть все данные из хранилища
$this->getData();

// Получение записи по ключу
$repository->getItemByKey($key);

// Установить время кеширования (при инициализации данных)
$repository->cache($minutes);

// Обновить кеш (отчищает текущий кеш и записывает текущие данные в кеш)
$repository->updateCache($minutes);
```

## Загрузчики данных

Загрузчики содержат инструкции о том откуда и какие данные необходимо загружать в хранилище и имя хранилища. Есть 4 типа загрузкиков данных:

* DataEntityLoader - работает с классом DataManager.
* HLBlockLoader - работает с HL блоками.
* IblockLoader - работает с инфоблоками.
* SimpleLoader - произвольные данные.

### DataEntityLoader

```php
use Alex19pov31\LinkedData\Loaders\DataEntityLoader;
use Bitrix\Iblock\IblockTable;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных без указания имени хранилища
 */
$dataManager = new IblockTable;
$loader = new DataEntityLoader($dataManager);
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по сгенерированному имени
 */
$tableName = IblockTable::getTableName(); // b_iblock
$repositoryName = DataEntityLoader::getRepositoryName($tableName); // table_b_iblock
$repository = Storage::getRepository($repositoryName);
```


```php
use Alex19pov31\LinkedData\Loaders\DataEntityLoader;
use Bitrix\Iblock\IblockTable;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных с указанием имени хранилища
 */
$dataManager = new IblockTable;
$loader = new DataEntityLoader($dataManager, 'iblocks');
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по заданному имени
 */
$repository = Storage::getRepository('iblocks')->init();
$repository->isEmpty(); // false
$repository->getCount(); // 1
$repository->getData(); // [['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']]
$repository->getItemByKey(1); // ['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']
```

### HLBlockLoader

```php
use Alex19pov31\LinkedData\Loaders\HLBlockLoader;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных без указания имени хранилища
 */
$loader = new HLBlockLoader('table_color');
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по сгенерированному имени
 */
$repositoryName = HLBlockLoader::getRepositoryName('table_name'); // hl_table_color
$repository = Storage::getRepository($repositoryName);
```

```php
use Alex19pov31\LinkedData\Loaders\HLBlockLoader;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных с указанием имени хранилища
 */
$loader = new HLBlockLoader('table_color', 'colors');
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по заданному имени
 */
$repository = Storage::getRepository('colors')->init();
$repository->isEmpty(); // false
$repository->getCount(); // 1
$repository->getData(); // [['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']]
$repository->getItemByKey(1); // ['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']
```

### IblockLoader

```php
use Alex19pov31\LinkedData\Loaders\IblockLoader;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных без указания имени хранилища
 */
$loader = new IblockLoader('iblock_code');
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по сгенерированному имени
 */
$repositoryName = IblockLoader::getRepositoryName('iblock_code'); // iblock_iblock_code
$repository = Storage::getRepository($repositoryName);
```

```php
use Alex19pov31\LinkedData\Loaders\IblockLoader;
use Alex19pov31\LinkedData\Storage;

/**
 * Регистрация загрузчика данных с указанием имени хранилища
 */
$loader = new IblockLoader('iblock_code', 'iblock_data');
$loader->sort(['ID' => 'ASC'])
    ->filter(['FIELD1' => 'VALUE'])
    ->select(['ID', 'FIELD1', 'FIELD2']);

/**
 * Получение доступа к хранилищу по заданному имени
 */
$repository = Storage::getRepository('iblock_data')->init();
$repository->isEmpty(); // false
$repository->getCount(); // 1
$repository->getData(); // [['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']]
$repository->getItemByKey(1); // ['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']
```

### SimpleLoader

```php
use Alex19pov31\LinkedData\Loaders\SimpleLoader;
use Alex19pov31\LinkedData\Storage;
use Bitrix\Main\Application;

/**
 * Регистрация загрузчика данных с указанием имени хранилища
 */
$loader = new SimpleLoader('repository_name');
$loader->setDataLoadCallback(function() {
    $conn = Application::getInstance()->getConnection();
    $res = $conn->query('select `ID`, `FIELD1`, `FIELD2` * FROM `b_iblock`')
    return $res->fetchAll();
});
$loader->setItemLoadCallback(function($key) {
    $conn = Application::getInstance()->getConnection();
    $res = $conn->query('select `ID`, `FIELD1`, `FIELD2` * FROM `b_iblock` WHERE `ID` = '.$key)
    return $res->fetch();
});

/**
 * Получение доступа к хранилищу по заданному имени
 */
$repository = Storage::getRepository('repository_name')->init();
$repository->isEmpty(); // false
$repository->getCount(); // 1
$repository->getData(); // [['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']]
$repository->getItemByKey(1); // ['ID' => 1, 'FIELD1' => 'VALUE', 'FIELD2' => '']
```