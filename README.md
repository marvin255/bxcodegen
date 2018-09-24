Генератор кода для 1С Битрикс
==================================

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxcodegen/v/stable.png)](https://packagist.org/packages/marvin255/bxcodegen)
[![License](https://poser.pugx.org/marvin255/bxcodegen/license.svg)](https://packagist.org/packages/marvin255/bxcodegen)
[![Build Status](https://travis-ci.org/marvin255/bxcodegen.svg?branch=master)](https://travis-ci.org/marvin255/bxcodegen)

Генератор кода для 1С Битрикс. Автоматическое создание новых компонентов, модулей.



Установка
---------

Устанавливается с помощью [Composer](https://getcomposer.org/doc/00-intro.md).

Выполните команду в папке вашего проекта:

```
composer require marvin255/bxcodegen:~0.9
```



Описание
--------

Создает из шаблонов часто используемые элементы Битрикса, например, компоненты или модули. Позволяет провести настройку перед созданием, добавить свой собственный шаблон или настроить старый.

Состоит из четырех частей:

1. `marvin255\bxcodegen\Bxcodegen` - объект кодогенератора, который служит связующей шиной для всех остальных элементов.

2. `marvin255\bxcodegen\ServiceLocator` - объект service locator, который позволяет передавать сервисы между различными генераторами, например, сервис для обработки twig шаблонов, или сервис, который возвращает пути до стандартных папок проекта.

3. `marvin255\bxcodegen\generator\GeneratorInterface` - объект генератора, который реализует данный интерфейс, получает массив настроек и service locator, непосредственно формирует код.

4. `marvin255\bxcodegen\service\options\CollectionInterface` - объект настроек, передает настройки для каждого конкретного генератора и для каждого конкретного случая создания кода. Настройки есть нескольких уровней:

    1. дефолтные настройки генератора - указываются непосредственно в самом генераторе,

    2. настройки проекта - указываются в файле настроек проекта `.bxcodegen.yaml`, перезаписывают дефолтные настройки генератора,

    3. настройки команды - указываются непосредственно в том коде, который запускает генератор (например, в аргументах команд консоли), перезаписывают дефолтные настройки и настройки проекта.



Дополнительные требования к установке
-------------------------------------

Прежде всего, нужно создать консольный скрипт, который позволит использовать 1С Битрикс в консоли. Пример реализации такого скрипта с использованием [Symfony console](https://github.com/symfony/console):

```php
#!/usr/bin/env php
<?php

//Данный файл называется cli.php и расположен на уровень выше document root веб-сервера (папка web).
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/web');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

//Файл настроек генератора должен быть создан и находиться рядом с этим скриптом.
define('BXCODEGEN_YAML_PATH', __DIR__ . '/.bxcodegen.yaml');

//Отключаем сбор статистики, проверку событий и агентов.
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('CHK_EVENT', true);

//Подключаем загрузку классов composer.
//Файл composer.json расположен на том же уровне, что и данный файл.
//Все зависимости из composer установлены.
require_once __DIR__ . '/vendor/autoload.php';

//Подключаем ядро битрикса.
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//Создаем приложение Symfony console.
$application = new \Symfony\Component\Console\Application;

//Регистрируем команды для кодогенератора.
\marvin255\bxcodegen\Factory::registerCommands($application, BXCODEGEN_YAML_PATH);

//Запускаем приложение на исполнение.
$application->run();
```



Файл настроек
-------------

Для проекта можно создать файл настроек в формате yaml, через который можно добавить новые генераторы или новые сервисы или переопределить настройки для дефолтных генераторов и дефолтных сервисов.

В файле настроек две основных секции: сервисы и генераторы.

Сервисы определяются ключом, который будет выступать в качестве имени сервиса и массивом, где первым элементом всегда должно идти имя класса сервиса, а оставшиеся элементы будут переданы в качестве параметров в конструктор. Например:

```yaml
services:
    myServiceName:
        - Path\To\ClassHandler
        - constructor_param_string_1
        - constructor_param_string_2
```

Генераторы определяются ключом, который будет выступать в качестве имени генератора и ассоциативным массивом настроек генератора, которые будут переданы генератору в качестве настроек проекта. Обязательно должен быть указан параметр `class`, в котором должен быть указан класс объекта генератора. Например:

```yaml
services:
    component:
        class: Path\To\ClassHandler
```

Настройки проекта переопределяют дефолтные настройки библиотеки, которые можно представить в виде следующего yaml:

```yaml
services:
    pathManager:
        - marvin255\bxcodegen\service\path\PathManager
        - '@currDir'
        - {components: /web/local/components, modules: /web/local/modules}
    renderer:
        - marvin255\bxcodegen\service\renderer\Twig
    copier:
        - marvin255\bxcodegen\service\filesystem\Copier

generators:
    component:
        class: marvin255\bxcodegen\generator\Component
    module:
        class: marvin255\bxcodegen\generator\Module
    orm:
        class: marvin255\bxcodegen\generator\Orm
    rocketeer:
        class: marvin255\bxcodegen\generator\Rocketeer
```

**Файл настроек полностью заменит дефолтные опции!**

Внутри файла настроек можно переопределить любые параметры дефолтных генераторов, либо добавить описания новых генераторов. Для новых генераторов нужно будет либо зарегистрировать отдельно созданную команду в консольном скрипте.



Команды, предоставляемые вместе с библиотекой
---------------------------------------------

1. `bxcodegen:module vendor.module_name` - создать модуль с именем `vendor.module_name`,

2. `bxcodegen:component vendor.module_name:component_name` - создать компонент с именем `vendor.module_name:component_name`.

3. `bxcodegen:orm table_name '\Class\Name\ModelTable'` - создать orm с классом `\Class\Name\ModelTable` для таблицы `table_name`.

4. `bxcodegen:rocketeer` - создать конфиг проекта для rocketeer.
