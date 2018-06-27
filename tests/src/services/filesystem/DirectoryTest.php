<?php

namespace marvin255\bxcodegen\tests\services\filesystem;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\services\filesystem\Directory;
use marvin255\bxcodegen\services\filesystem\FileInterface;
use InvalidArgumentException;

class DirectoryTest extends BaseCase
{
    /**
     * @var string
     */
    protected $folderPath;
    /**
     * @var array
     */
    protected $info = [];

    /**
     * @test
     */
    public function testEmptyPathToFolderException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory('        ');
    }

    /**
     * @test
     */
    public function testWrongPathToFolderException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory('not_root/path');
    }

    /**
     * @test
     */
    public function testWrongFileClassException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory(sys_get_temp_dir(), get_class($this));
    }

    /**
     * @test
     */
    public function testGetPathname()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['pathname'], $dir->getPathname());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['path'], $dir->getPath());
    }

    /**
     * @test
     */
    public function testGetFolderName()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['folderName'], $dir->getFoldername());
    }

    /**
     * @test
     */
    public function testCreateAndDelete()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame(false, $dir->isExists());
        $this->assertSame(true, $dir->create());
        $this->assertSame(true, $dir->isExists());

        $testFolderName = "{$this->folderPath}/test_folder_name_" . mt_rand();
        mkdir($testFolderName);
        $testNestedFileName = "{$testFolderName}/test_nested_file_name_" . mt_rand() . '.test';
        file_put_contents($testNestedFileName, 'test');
        $testFileName = "{$this->folderPath}/test_file_name_" . mt_rand() . '.test';
        file_put_contents($testFileName, 'test');

        $this->assertSame(true, $dir->delete());
        $this->assertSame(false, $dir->isExists());
        $this->assertDirectoryNotExists($this->folderPath);
    }

    /**
     * @test
     */
    public function testWrongChildDirName()
    {
        $dir = new Directory($this->folderPath);

        $this->expectException(InvalidArgumentException::class);
        $dir->createChildDirectory('../');
    }

    /**
     * @test
     */
    public function testWrongChildFileName()
    {
        $dir = new Directory($this->folderPath);

        $this->expectException(InvalidArgumentException::class);
        $dir->createChildFile('../');
    }

    /**
     * @test
     */
    public function testIterator()
    {
        $dir = new Directory($this->folderPath);
        $childDirName = "child_dir_name_" . mt_rand();
        $childFileName = "child_file_name_" . mt_rand();

        $dir->create();
        mkdir("{$this->folderPath}/{$childDirName}");
        $testNestedFileName = "{$this->folderPath}/{$childDirName}/test_nested_file_name_" . mt_rand();
        file_put_contents($testNestedFileName, 'test');
        $testFileName = "{$this->folderPath}/{$childFileName}";
        file_put_contents($testFileName, 'test');

        $arEtalon = [$childDirName, $childFileName];
        $arTest = [];
        foreach ($dir as $key => $item) {
            $arTest[$key] = ($item instanceof FileInterface)
                ? $item->getFilename()
                : $item->getFoldername();
        }
        sort($arEtalon);
        sort($arTest);

        $this->assertSame($arEtalon, $arTest);
    }

    /**
     * Подготоваливает директорию для тестов и информацию о ней.
     */
    public function setUp()
    {
        $folderName = 'folder_name_' . mt_rand();
        $rootPath = sys_get_temp_dir() . '/root_folder_' . mt_rand();

        $this->folderPath = $rootPath . '/' . $folderName;
        $this->info = [
            'pathname' => $this->folderPath,
            'path' => $rootPath,
            'folderName' => $folderName,
        ];

        parent::setUp();
    }

    /**
     * Удаляет тестовую директорию и все е содержимое.
     */
    public function tearDown()
    {
        if (is_dir($this->folderPath)) {
            $it = new \RecursiveDirectoryIterator(
                $this->folderPath,
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
            rmdir($this->folderPath);
        }
        if (is_dir($this->info['path'])) {
            rmdir($this->info['path']);
        }

        parent::tearDown();
    }
}
