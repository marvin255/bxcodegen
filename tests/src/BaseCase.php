<?php

namespace marvin255\bxcodegen\tests;

use PHPUnit\Framework\TestCase;

/**
 * Базовый класс для всех тестов.
 */
class BaseCase extends TestCase
{
    /**
     * Удаляет содержимое временной папки, которую создал тест.
     *
     * @param string $folderPath
     */
    protected function removeDir($folderPath)
    {
        if (is_dir($folderPath)) {
            $it = new \RecursiveDirectoryIterator(
                $folderPath,
                \RecursiveDirectoryIterator::SKIP_DOTS
            );
            $files = new \RecursiveIteratorIterator(
                $it,
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($folderPath);
        }
    }
}
