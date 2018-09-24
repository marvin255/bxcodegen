<?php

namespace marvin255\bxcodegen\service\path;

use marvin255\bxcodegen\service\filesystem\PathHelper;
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
     * Для путей, в которых указаны псевдонимы, например @modules/my.module, при
     * определении абсолютного пути псведонимы будут заменены на соответствующие
     * папки.
     *
     * @var array
     */
    protected $aliases = [];
    /**
     * Массив пространств имен для классов php и соответствующих им папок в
     * файловой системе. Пути допускабт использование псевдонимов.
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * @param string $absolutePath
     * @param array  $aliases
     * @param array  $namespaces
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($absolutePath, array $aliases = [], array $namespaces = [])
    {
        $clearedAbsolutePath = PathHelper::getRealPath($absolutePath, true);
        if (!$clearedAbsolutePath || !is_dir($clearedAbsolutePath)) {
            throw new InvalidArgumentException(
                "{$absolutePath} ({$clearedAbsolutePath}) is not an existed directory"
            );
        }
        $this->absolutePath = $clearedAbsolutePath;

        foreach ($aliases as $alias => $path) {
            $this->setAlias($alias, $path);
        }
        foreach ($namespaces as $namespace => $path) {
            $this->setPathForNamespace($namespace, $path);
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

        $this->aliases[$alias] = PathHelper::unify($path);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAbsolutePath($path)
    {
        $aliasReplacedPath = $this->replaceAliases($path);

        return PathHelper::combine([$this->absolutePath, $aliasReplacedPath]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setPathForNamespace($namespace, $path)
    {
        $unifiedNamespace = $this->unifyNamespace($namespace);
        if (!preg_match('/^[a-zA-Z0-9_\\\]{3,}$/', $unifiedNamespace)) {
            throw new InvalidArgumentException("Invalid namespace {$namespace}");
        }

        $this->namespaces[$unifiedNamespace] = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAbsolutePathForClass($className)
    {
        $unifiedClassName = $this->unifyNamespace($className);
        list($selectedNamespace, $selectedPath) = $this->selectNamespace($unifiedClassName);

        if ($selectedNamespace && $selectedNamespace !== $unifiedClassName) {
            $tailNamespace = $this->unifyNamespace(substr($unifiedClassName, strlen($selectedNamespace)));
            $explodeTailNamespace = explode('\\', $tailNamespace);
            foreach ($explodeTailNamespace as $key => $item) {
                if (($key + 1) === count($explodeTailNamespace)) {
                    $item = strtolower($item) . '.php';
                }
                $selectedPath = PathHelper::combine([$selectedPath, $item]);
            }
        }

        return $selectedPath;
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
     * Выбирает подходящее для класса пространство имен.
     *
     * Пробует подобрать самое длинное, которое входит в имя класса, если ничего
     * подобрать не удается, то пробует найти соответствующий модуль согласно правилам
     * автозагрузки в битриксе.
     *
     * @param string $className
     *
     * @return array
     */
    protected function selectNamespace($className)
    {
        $selectedNamespace = null;
        $selectedPath = null;
        foreach ($this->namespaces as $namespace => $path) {
            if (
                strpos($className, $namespace) === 0
                && (!$selectedNamespace || strlen($namespace) > strlen($selectedNamespace))
            ) {
                $selectedNamespace = $namespace;
                $selectedPath = $this->getAbsolutePath($path);
            }
        }

        if (!$selectedNamespace) {
            $explodeClass = explode('\\', $className);
            if (count($explodeClass) > 2) {
                $moduleName = strtolower("{$explodeClass[0]}.{$explodeClass[1]}");
                $pathToModule = "@modules/{$moduleName}/lib";
                $absolutePathToModule = $this->getAbsolutePath($pathToModule);
                if (is_dir($absolutePathToModule)) {
                    $selectedNamespace = "{$explodeClass[0]}\\{$explodeClass[1]}";
                    $selectedPath = $absolutePathToModule;
                }
            }
        }

        return [$selectedNamespace, $selectedPath];
    }

    /**
     * Унифицирует пространства имен для внутреннего использования.
     *
     * @param string $namespace
     *
     * @return string
     */
    protected function unifyNamespace($namespace)
    {
        return trim($namespace, " \t\n\r\0\x0B\\");
    }
}
