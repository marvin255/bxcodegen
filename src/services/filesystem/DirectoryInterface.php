<?php

namespace marvin255\bxcodegen\services\filesystem;

use Iterator;

/**
 * Интерфейс для объекта, который инкапсулирует обращение к папке в локальной
 * файловой системе.
 */
interface DirectoryInterface extends Iterator
{
    /**
     * Возвращает путь и имя папки.
     *
     * @return string
     */
    public function getPathname();

    /**
     * Возвращает путь без имени папки.
     *
     * @return string
     */
    public function getPath();

    /**
     * Возвращает имя папки.
     *
     * @return string
     */
    public function getFoldername();

    /**
     * Возвращает true, если папка существует в файловой системе.
     *
     * @return bool
     */
    public function isExists();

    /**
     * Удаляет папку из файловой системы.
     *
     * @return bool
     */
    public function delete();

    /**
     * Создает папку и все родительские.
     *
     * @return bool
     */
    public function create();

    /**
     * Создает вложенную папку.
     *
     * @param string $name
     *
     * @return \marvin255\fias\service\filesystem\DirectoryInterface
     */
    public function createChildDirectory($name);

    /**
     * Создает вложенный файл.
     *
     * @param string $name
     *
     * @return \marvin255\fias\service\filesystem\FileInterface
     */
    public function createChildFile($name);
}
