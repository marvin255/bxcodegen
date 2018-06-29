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
    public function copyDir(DirectoryInterface $source, DirectoryInterface $destination);

    /**
     * Копирует файл.
     *
     * @param \marvin255\bxcodegen\services\filesystem\FileInterface $source
     * @param \marvin255\bxcodegen\services\filesystem\FileInterface $destination
     *
     * @return self
     */
    public function copyFile(FileInterface $source, FileInterface $destination);
}
