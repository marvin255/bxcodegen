<?php

namespace marvin255\bxcodegen\service\filesystem;

use InvalidArgumentException;

/**
 * Объект, который инкапсулирует обращение к файлу в локальной
 * файловой системе.
 */
class File implements FileInterface
{
    /**
     * Абсолютный путь к файлу.
     *
     * @var string
     */
    protected $absolutePath = null;
    /**
     * Данные о файле, возвращаемые pathinfo.
     *
     * @var array
     */
    protected $info = [];

    /**
     * Конструктор. Задает абсолютный путь к файлу.
     *
     * Папка должна существовать и должна быть доступна на запись.
     *
     * @param string $absolutePath
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($absolutePath)
    {
        $unified = PathHelper::unify($absolutePath);
        if (empty($unified)) {
            throw new InvalidArgumentException(
                "absolutePath parameter can't be empty, got: {$unified}"
            );
        } elseif (!PathHelper::isAbsolute($unified)) {
            throw new InvalidArgumentException(
                "absolutePath parameter must contains absolute path to file, got: {$unified}"
            );
        }

        $info = pathinfo($unified);
        $info['dirname'] = $info['dirname'];

        $this->absolutePath = PathHelper::combine([$info['dirname'], $info['basename']]);
        $this->info = $info;
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
        return isset($this->info['dirname']) ? $this->info['dirname'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getFilename()
    {
        return isset($this->info['filename']) ? $this->info['filename'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return isset($this->info['extension']) ? $this->info['extension'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getBasename()
    {
        return isset($this->info['basename']) ? $this->info['basename'] : null;
    }

    /**
     * @inheritdoc
     */
    public function isExists()
    {
        return file_exists($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $return = false;
        if ($this->isExists()) {
            $return = unlink($this->absolutePath);
        }

        return $return;
    }
}
