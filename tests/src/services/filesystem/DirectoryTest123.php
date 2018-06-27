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

    public function testEmptyPathToFolderException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory('        ');
    }

    public function testWrongPathToFolderException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory('not_root/path');
    }

    public function testWrongFileClassException()
    {
        $this->expectException(InvalidArgumentException::class);
        $dir = new Directory(sys_get_temp_dir(), get_class($this));
    }

    public function testGetPathname()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['pathname'], $dir->getPathname());
    }

    public function testGetPath()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['path'], $dir->getPath());
    }

    public function testGetFolderName()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame($this->info['folderName'], $dir->getFoldername());
    }

    public function testCreateAndDelete()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame(false, $dir->isExists());
        $this->assertSame(true, $dir->create());
        $this->assertSame(true, $dir->isExists());

        $testFolderName = "{$this->folderPath}/{$this->faker()->unique()->word}";
        mkdir($testFolderName);
        $testNestedFileName = "{$testFolderName}/{$this->faker()->unique()->word}.{$this->faker()->unique()->word}";
        file_put_contents($testNestedFileName, 'test');
        $testFileName = "{$this->folderPath}/{$this->faker()->unique()->word}.{$this->faker()->unique()->word}";
        file_put_contents($testFileName, 'test');

        $this->assertSame(true, $dir->delete());
        $this->assertSame(false, $dir->isExists());
        $this->assertDirectoryNotExists($this->folderPath);
    }

    public function testCreateAndEmpty()
    {
        $dir = new Directory($this->folderPath);

        $this->assertSame(false, $dir->isExists());
        $this->assertSame(true, $dir->create());
        $this->assertSame(true, $dir->isExists());

        $testFolderName = "{$this->folderPath}/{$this->faker()->unique()->word}";
        mkdir($testFolderName);
        $testNestedFileName = "{$testFolderName}/{$this->faker()->unique()->word}.{$this->faker()->unique()->word}";
        file_put_contents($testNestedFileName, 'test');
        $testFileName = "{$this->folderPath}/{$this->faker()->unique()->word}.{$this->faker()->unique()->word}";
        file_put_contents($testFileName, 'test');

        $this->assertSame(true, $dir->deleteChildren());
        $this->assertSame(true, $dir->isExists());
        $this->assertDirectoryExists($this->folderPath);
        $this->assertDirectoryNotExists($testFolderName);
        $this->assertFileNotExists($testFileName);
    }

    public function testWrongChildDirName()
    {
        $dir = new Directory($this->folderPath);

        $this->expectException(InvalidArgumentException::class);
        $dir->createChildDirectory('../');
    }

    public function testWrongChildFileName()
    {
        $dir = new Directory($this->folderPath);

        $this->expectException(InvalidArgumentException::class);
        $dir->createChildFile('../');
    }

    public function testFindFilesByPattern()
    {
        $pattern = '*test.*';
        $patternedFiles = [
            '1_test.csv',
            '2_test.xml',
            '3_test.test',
        ];
        $unpatternedFiles = [
            'no_pattern.pattern',
            'test_no_pattern.xml',
        ];

        $dir = new Directory($this->folderPath);
        $dir->create();
        foreach (array_merge($patternedFiles, $unpatternedFiles) as $file) {
            file_put_contents($this->folderPath . '/' . $file, $file);
        }
        $findedFiles = $dir->findFilesByPattern($pattern);

        $this->assertCount(count($patternedFiles), $findedFiles);
        foreach ($findedFiles as $findedFile) {
            $this->assertContains($findedFile->getBasename(), $patternedFiles);
            $this->assertNotContains($findedFile->getBasename(), $unpatternedFiles);
        }
    }

    public function testIterator()
    {
        $dir = new Directory($this->folderPath);
        $childDirName = $this->faker()->unique()->word;
        $childFileName = $this->faker()->unique()->word;

        $dir->create();
        mkdir("{$this->folderPath}/{$childDirName}");
        $testNestedFileName = "{$this->folderPath}/{$childDirName}/{$this->faker()->unique()->word}";
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

    public function setUp()
    {
        $folderName = $this->faker()->unique()->word . mt_rand();
        $rootPath = sys_get_temp_dir() . '/' . $this->faker()->unique()->word . mt_rand();

        $this->folderPath = $rootPath . '/' . $folderName;
        $this->info = [
            'pathname' => $this->folderPath,
            'path' => $rootPath,
            'folderName' => $folderName,
        ];

        parent::setUp();
    }

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
