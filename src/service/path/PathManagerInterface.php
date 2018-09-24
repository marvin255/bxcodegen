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

    /**
     * Задает путь в файловой системе для определенного пространства имен.
     * Допустимо использование алиасов.
     *
     * @param string $namespace
     * @param string $path
     *
     * @return self
     */
    public function setPathForNamespace($namespace, $path);

    /**
     * Возвращает абсолютный путь к файлу, в котором расположен указанный класс.
     *
     * Если нетпрямого указания пути, то пытается найти путь для самого длинного
     * пространства имен, в которое входит класс, и добавить к нему остальной путь
     * согласно соглашениям для автозагрузчика битрикс.
     *
     * @param string $className
     *
     * @return string|null
     */
    public function getAbsolutePathForClass($className);
}
