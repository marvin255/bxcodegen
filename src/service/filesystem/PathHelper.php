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
     * Объединяет два отрезка пути в один.
     *
     * @param string $pathHead
     * @param string $pathTail
     *
     * @return string
     */
    public static function combine($pathHead, $pathTail)
    {
        $separator = self::getDirectorySeparator();
        $unifiedHead = PathHelper::unify($pathHead);
        $unifiedTail = PathHelper::unify($pathTail);

        return $unifiedHead . $separator . ltrim($unifiedTail, $separator);
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
