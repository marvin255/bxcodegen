<?php

namespace marvin255\bxcodegen\service\path;

use InvalidArgumentException;

/**
 * Объект, который позволяет получить полный путь к какой-либо папке
 * внутри проекта или получить путь, который задан псевдонимом папки.
 */
class PathManager implements PathManagerInterface
{
    /**
     * Абсолютный путь до рабочей папкипроекта. При определении абсолютного
     * пути всегда будет подставляться в качестве префикса. Папка должна
     * существовать в файловой системе.
     *
     * @var string
     */
    protected $absolutePath;
    /**
     * Массив псевдонимов папок вида "название псевдонима => относительный путь к папке".
     * Для путей, в которох указаны псевдонимы, например @modules/my.module, при
     * определении абсолютного пути псведонимы будут заменены на соответствующие
     * папки.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * @param string $absolutePath
     * @param array  $aliases
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($absolutePath, array $aliases = [])
    {
        $clearedAbsolutePath = realpath($absolutePath);
        if (!$clearedAbsolutePath || !is_dir($clearedAbsolutePath)) {
            throw new InvalidArgumentException(
                "{$absolutePath} is not an existed directory"
            );
        }
        $this->absolutePath = rtrim($clearedAbsolutePath, '/\\');

        foreach ($aliases as $alias => $path) {
            $this->setAlias($alias, $path);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setAlias($alias, $path)
    {
        if (!preg_match('/^[a-z0-9_]{3,}$/', $alias)) {
            throw new InvalidArgumentException(
                'Alias name must consist of more than 2 symbols of latin, digits and _.'
                . " Got: {$alias}"
            );
        }

        $this->aliases[$alias] = $this->convertAndTrimRelatedPath($path);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getAbsolutePath($path)
    {
        $trimmedPath = $this->convertAndTrimRelatedPath($path);
        $aliasReplacedPath = $this->replaceAliases($trimmedPath);

        return $this->absolutePath . '/' . $aliasReplacedPath;
    }

    /**
     * Заменяет псевдонимы в пути на их значения.
     *
     * @param string $path
     *
     * @return string
     */
    protected function replaceAliases($path)
    {
        $toSearch = array_map(function ($item) {
            return "@{$item}";
        }, array_keys($this->aliases));

        $toReplace = array_values($this->aliases);

        return str_replace($toSearch, $toReplace, $path);
    }

    /**
     * Приводит путь к общему относительному виду.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function convertAndTrimRelatedPath($path)
    {
        if ($this->isPathUseDots($path)) {
            throw new InvalidArgumentException(
                "Path must not use dots. Got: {$path}"
            );
        }

        return trim($path, " \t\n\r\0\x0B/\/");
    }

    /**
     * Проверяет использованы ли в пути точки для перехода на верхний уровень.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isPathUseDots($path)
    {
        return (bool) preg_match('#(^|/)\.{1,2}(/|$)#', $path);
    }
}
