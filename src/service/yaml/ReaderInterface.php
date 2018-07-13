<?php

namespace marvin255\bxcodegen\service\yaml;

/**
 * Интерфейс для объекта, который парсит yaml файлы.
 */
interface ReaderInterface
{
    /**
     * Парсит содержимое yaml файла, путь к которому указан в параметре.
     *
     * @param string $pathToFile
     *
     * @return array
     */
    public function parseFromFile($pathToFile);
}
