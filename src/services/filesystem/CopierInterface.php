<?php

namespace marvin255\bxcodegen\services\filesystem;

/**
 * Интерфейс для объекта, который рекурсивно копирует содержимое одной папки
 * в другую.
 */
interface CopierInterface
{
    /**
     * Рекурсивно копирует содержимое одной папки в другую.
     *
     * @param \marvin255\bxcodegen\services\filesystem\DirectoryInterface $source
     * @param \marvin255\bxcodegen\services\filesystem\DirectoryInterface $destination
     *
     * @return self
     */
    public function copy(DirectoryInterface $source, DirectoryInterface $destination);
}
