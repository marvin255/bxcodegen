<?php

namespace marvin255\bxcodegen\tests;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Базовый класс для всех тестов.
 */
class BaseCase extends TestCase
{
    /**
     * @var array
     */
    private $baseDir;

    /**
     * Возвращает путь до базовой папки для тестов.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getBaseDir()
    {
        if ($this->baseDir === null) {
            $this->baseDir = sys_get_temp_dir();
            if (!$this->baseDir || !is_writable($this->baseDir)) {
                throw new RuntimeException(
                    "Can't find or wrtite temporary folder: {$this->baseDir}"
                );
            }
            $this->baseDir .= DIRECTORY_SEPARATOR . 'bxcodegen';
        }

        return $this->baseDir;
    }

    /**
     * Создает директорию во временной папке и возвращает путь до нее.
     *
     * @param string $prefix
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getTempFolder($prefix = null)
    {
        if ($prefix === null) {
            $prefix = preg_replace('/[^a-zA-Z0-9_]+/', '_', get_class($this));
            $prefix = strtolower(trim($prefix, " \t\n\r\0\x0B_"));
        }

        $pathToFolder = $this->getBaseDir() . DIRECTORY_SEPARATOR . $prefix;
        if (is_dir($pathToFolder)) {
            $this->removeDir($pathToFolder);
        }
        if (!mkdir($pathToFolder, 0777, true)) {
            throw new RuntimeException(
                "Can't mkdir {$pathToFolder} folder"
            );
        }

        $this->createdDirs[] = $pathToFolder;

        return $pathToFolder;
    }

    /**
     * Удаляет содержимое временной папки, которую создал тест.
     *
     * @param string $folderPath
     */
    protected function removeDir($folderPath)
    {
        if (is_dir($folderPath)) {
            $it = new RecursiveDirectoryIterator(
                $folderPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            );
            $files = new RecursiveIteratorIterator(
                $it,
                RecursiveIteratorIterator::CHILD_FIRST
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

    /**
     * Удаляет тестовую директорию и все ее содержимое.
     */
    public function tearDown()
    {
        if ($this->baseDir) {
            $this->removeDir($this->baseDir);
        }

        parent::tearDown();
    }
}
