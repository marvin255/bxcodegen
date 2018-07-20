<?php

namespace marvin255\bxcodegen\service\filesystem;

/**
 * Интерфейс для объекта, который рекурсивно копирует содержимое одной папки
 * в другую.
 */
interface CopierInterface
{
    /**
     * Рекурсивно копирует содержимое одной папки в другую.
     *
     * @param \marvin255\bxcodegen\service\filesystem\DirectoryInterface $source
     * @param \marvin255\bxcodegen\service\filesystem\DirectoryInterface $destination
     *
     * @return self
     */
    public function copyDir(DirectoryInterface $source, DirectoryInterface $destination);

    /**
     * Копирует файл.
     *
     * @param \marvin255\bxcodegen\service\filesystem\FileInterface $source
     * @param \marvin255\bxcodegen\service\filesystem\FileInterface $destination
     *
     * @return self
     */
    public function copyFile(FileInterface $source, FileInterface $destination);

    /**
     * Добавляет коллбэк для трансформации данных при копировании.
     *
     * @param callable $transformer
     *
     * @return self
     */
    public function addTransformer($transformer);

    /**
     * Очищает список коллбэков для трансформации данных при копировании.
     *
     * @return self
     */
    public function clearTransformers();
}
