<?php

namespace marvin255\bxcodegen\service\filesystem;

use CallbackFilterIterator;
use DirectoryIterator;
use RuntimeException;
use InvalidArgumentException;

/**
 * Объект, который инкапсулирует обращение к папке в локальной файловой системе.
 */
class Directory implements DirectoryInterface
{
    /**
     * Абсолютный путь к папке.
     *
     * @var string
     */
    protected $absolutePath = null;
    /**
     * Класс для создания новых файлов.
     *
     * @var string
     */
    protected $fileClass = null;
    /**
     * Внутренний итератор для обхода вложенных файлов и папок.
     *
     * @var DirectoryIterator
     */
    protected $iterator = null;

    /**
     * Конструктор. Задает абсолютный путь к папке, а так же классы для
     * создания вложенных папок и файлов.
     *
     * @param $absolutePath
     * @param $fileClass
     */
    public function __construct($absolutePath, $fileClass = '\\marvin255\\bxcodegen\\service\\filesystem\\File')
    {
        $unified = PathHelper::unify($absolutePath);
        if (!$unified) {
            throw new InvalidArgumentException(
                "absolutePath parameter can't be empty, got: {$absolutePath}"
            );
        }

        if (!PathHelper::isAbsolute($unified)) {
            throw new InvalidArgumentException(
                "absolutePath must starts from root, got: {$absolutePath}"
            );
        }

        if (!is_subclass_of($fileClass, FileInterface::class)) {
            throw new InvalidArgumentException(
                "{$fileClass} must implements a FileInterface"
            );
        }

        $this->absolutePath = $unified;
        $this->fileClass = $fileClass;
    }

    /**
     * @inheritdoc
     */
    public function getPathname()
    {
        return $this->absolutePath;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return dirname($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function getFoldername()
    {
        return pathinfo($this->absolutePath, PATHINFO_BASENAME);
    }

    /**
     * @inheritdoc
     */
    public function isExists()
    {
        return (bool) is_dir($this->absolutePath);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function delete()
    {
        $return = false;
        if ($this->isExists()) {
            foreach ($this as $child) {
                $child->delete();
            }
            if (!rmdir($this->getPathname())) {
                throw new RuntimeException("Can't delete folder: " . $this->getPathname());
            }
            $return = true;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function create()
    {
        $return = false;
        if (!$this->isExists()) {
            $arPath = PathHelper::split($this->getPathname());
            $current = '';
            foreach ($arPath as $folder) {
                $current = PathHelper::combine([$current, $folder]);
                if (is_dir($current)) {
                    continue;
                }
                if (!mkdir($current)) {
                    throw new RuntimeException("Can't create {$current} folder");
                }
            }
            $return = true;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createChildDirectory($name)
    {
        if (
            strpos($name, '\\') !== false
            || strpos($name, '/') !== false
            || strpos($name, '..') !== false
        ) {
            throw new InvalidArgumentException("Wrong folder name {$name}");
        }

        return new self($this->absolutePath . '/' . $name, $this->fileClass);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createChildFile($name)
    {
        if (
            strpos($name, '\\') !== false
            || strpos($name, '/') !== false
            || strpos($name, '..') !== false
        ) {
            throw new InvalidArgumentException("Wrong file name {$name}");
        }

        $class = $this->fileClass;

        return new $class($this->absolutePath . '/' . $name);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        $item = $this->getIterator()->current();

        if ($item->isDir()) {
            $return = $this->createChildDirectory($item->getFilename());
        } elseif ($item->isFile()) {
            $return = $this->createChildFile($item->getFilename());
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->getIterator()->key();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->getIterator()->next();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->getIterator()->rewind();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->getIterator()->valid();
    }

    /**
     * Возвращает внутренний объект итератора для перебора содержимого данной папки.
     *
     * @return \DirectoryIterator
     */
    protected function getIterator()
    {
        if ($this->iterator === null && $this->isExists()) {
            $dirIterator = new DirectoryIterator($this->getPathname());
            $this->iterator = new CallbackFilterIterator($dirIterator, function ($current, $key, $iterator) {
                return !$iterator->isDot();
            });
        }

        return $this->iterator;
    }
}
