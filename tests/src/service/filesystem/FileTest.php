<?php

namespace marvin255\bxcodegen\tests\service\filesystem;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\filesystem\File;
use InvalidArgumentException;

class FileTest extends BaseCase
{
    /**
     * @var string
     */
    protected $tempFile;
    /**
     * @var array
     */
    protected $info = [];

    /**
     * @test
     */
    public function testEmptyAbsolutePathException()
    {
        $this->expectException(InvalidArgumentException::class);
        $file = new File('');
    }

    /**
     * @test
     */
    public function testGetPathName()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->tempFile, $file->getPathname());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['dirname'], $file->getPath());
    }

    /**
     * @test
     */
    public function testGetFileName()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['filename'], $file->getFileName());
    }

    /**
     * @test
     */
    public function testGetExtension()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['extension'], $file->getExtension());
    }

    /**
     * @test
     */
    public function testGetBasename()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['basename'], $file->getBasename());
    }

    /**
     * @test
     */
    public function testDelete()
    {
        $file = new File($this->tempFile);

        $this->assertSame(true, $file->isExists());
        $this->assertSame(true, $file->delete());
        $this->assertSame(false, $file->isExists());
    }

    /**
     * Создает тестовый файл и подготавливает массив с информацией о нем.
     */
    public function setUp()
    {
        $rootPath = $this->getTempFolder();
        $name = $rootPath . '/file_name_' . mt_rand() . '.ext' . mt_rand();

        file_put_contents($name, mt_rand());

        $this->tempFile = $name;

        $this->info = pathinfo($this->tempFile);
        $this->info['dirname'] = realpath($this->info['dirname']);
        $this->info['extension'] = isset($this->info['extension'])
            ? $this->info['extension']
            : null;

        parent::setUp();
    }
}
