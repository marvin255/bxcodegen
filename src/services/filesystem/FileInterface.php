<?php

namespace marvin255\bxcodegen\services\filesystem;

/**
 * Интерфейс для объекта, который инкапсулирует обращение к файлу в локальной
 * файловой системе.
 */
interface FileInterface
{
    /**
     * Возвращает путь и имя файла.
     *
     * @return string
     */
    public function getPathname();

    /**
     * Возвращает путь без имени файла.
     *
     * @return string
     */
    public function getPath();

    /**
     * Возвращает имя файла (без расширения).
     *
     * @return string
     */
    public function getFilename();

    /**
     * Возвращает расширение файла.
     *
     * @return string
     */
    public function getExtension();

    /**
     * Возвращает полное имя файла (с расширением).
     *
     * @return string
     */
    public function getBasename();

    /**
     * Возвращает true, если файл существует в файловой системе.
     *
     * @return bool
     */
    public function isExists();

    /**
     * Удаляет файл из файловой системы.
     *
     * @return bool
     */
    public function delete();
}
