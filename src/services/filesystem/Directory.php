<?php

namespace marvin255\bxcodegen\services\filesystem;

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
     * Папка должна существовать и должна быть доступна на запись.
     *
     * @param $absolutePath
     * @param $fileClass
     */
    public function __construct($absolutePath, $fileClass = '\\marvin255\\bxcodegen\\services\\filesystem\\File')
    {
        if (trim($absolutePath, ' \t\n\r\0\x0B\\/') === '') {
            throw new InvalidArgumentException(
                "absolutePath parameter can't be empty"
            );
        }

        if (!preg_match('/^\/.*+$/', $absolutePath)) {
            throw new InvalidArgumentException(
                'absolutePath must starts from root'
            );
        }

        if (!is_subclass_of($fileClass, FileInterface::class)) {
            throw new InvalidArgumentException(
                "{$fileClass} must implements a FileInterface"
            );
        }

        $this->absolutePath = $absolutePath;
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
            $path = $this->getPathname();
            $arPath = explode('/', ltrim($path, '/\\'));
            $current = '';
            foreach ($arPath as $folder) {
                $current .= '/' . $folder;
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
        if (!preg_match('/^[^\/\\]+$/i', $name)) {
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
        if (!preg_match('/^[^\/\\]+$/i', $name)) {
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
