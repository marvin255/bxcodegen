<?php

namespace marvin255\bxcodegen\service\filesystem;

/**
 * Объект для проверки и преобразования путей файловой системы.
 */
class PathHelper
{
    /**
     * Преобразует путь в файловой системе к общему стандартному виду.
     *
     * @param string $path
     * @param bool   $getReal
     *
     * @return string
     */
    public static function unify($path)
    {
        $separator = self::getDirectorySeparator();
        $unified = trim($path, " \t\n\r\0\x0B");
        $unified = str_replace(['/', '\\'], $separator, $unified);
        $unified = rtrim($unified, $separator);

        return $unified;
    }

    /**
     * Возвращает канонизированнный абсолютный путь для указанного.
     *
     * @param string $path
     *
     * @return string|null
     */
    public static function getRealPath($path)
    {
        $real = realpath(self::unify($path));

        return $real ? self::unify($real) : null;
    }

    /**
     * Объединяет отрезки пути в общий путь.
     *
     * @param array $parts
     *
     * @return string
     */
    public static function combine(array $parts)
    {
        $separator = self::getDirectorySeparator();

        $unifiedParts = array_values($parts);
        array_walk($unifiedParts, function (&$part, $key) use ($separator) {
            $part = PathHelper::unify($part);
            $part = $key === 0 ? $part : ltrim($part, $separator);
        });

        return implode($separator, $unifiedParts);
    }

    /**
     * Разбивает путь на отрезки.
     *
     * @param string $path
     *
     * @return string
     */
    public static function split($parts)
    {
        $separator = self::getDirectorySeparator();

        return explode($separator, self::unify($parts));
    }

    /**
     * Проверяет является ли путь абсолютным (начинается ли от корня).
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolute($path)
    {
        return preg_match('#^/.*#', $path) || preg_match('#^[a-zA-Z]{1}:.*#', $path);
    }

    /**
     * Возвращает разделитель пути в файловой системе.
     *
     * @return string
     */
    public static function getDirectorySeparator()
    {
        return DIRECTORY_SEPARATOR;
    }
}
