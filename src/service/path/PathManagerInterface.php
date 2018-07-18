<?php

namespace marvin255\bxcodegen\service\path;

/**
 * Интерфейс для объекта, который позволяет получить полный путь к какой-либо папке
 * внутри проекта или получить путь, который задан псевдонимом папки.
 */
interface PathManagerInterface
{
    /**
     * Задает псевдонимы для папок.
     *
     * @param string $alias
     * @param string $path
     *
     * @return self
     */
    public function setAlias($alias, $path);

    /**
     * Возвращает абсолютный путь до папки или файла с учетом всех псевдонимов.
     *
     * @param string $path
     *
     * @return string
     */
    public function getAbsolutePath($path);
}
