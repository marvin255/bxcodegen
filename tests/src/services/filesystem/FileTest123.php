<?php

namespace marvin255\bxcodegen\tests\services\filesystem;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\filesystem\File;
use InvalidArgumentException;

class FileTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected $tempFile;
    /**
     * @var array
     */
    protected $info = [];

    public function testEmptyAbsolutePathException()
    {
        $this->expectException(InvalidArgumentException::class);
        $file = new File('');
    }

    public function testUnexistedPathException()
    {
        $this->expectException(InvalidArgumentException::class);
        $file = new File(sys_get_temp_dir() . '/empty/file.txt');
    }

    public function testGetPathName()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->tempFile, $file->getPathname());
    }

    public function testGetPath()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['dirname'], $file->getPath());
    }

    public function testGetFileName()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['filename'], $file->getFileName());
    }

    public function testGetExtension()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['extension'], $file->getExtension());
    }

    public function testGetBasename()
    {
        $file = new File($this->tempFile);

        $this->assertSame($this->info['basename'], $file->getBasename());
    }

    public function testDelete()
    {
        $file = new File($this->tempFile);

        $this->assertSame(true, $file->isExists());
        $this->assertSame(true, $file->delete());
        $this->assertSame(false, $file->isExists());
    }

    public function setUp()
    {
        $name = sys_get_temp_dir()
            . '/' . $this->faker()->unique()->word
            . '.' . $this->faker()->unique()->word;

        file_put_contents($name, mt_rand());

        $this->tempFile = $name;

        $this->info = pathinfo($this->tempFile);
        $this->info['dirname'] = realpath($this->info['dirname']);
        $this->info['extension'] = isset($this->info['extension'])
            ? $this->info['extension']
            : null;

        parent::setUp();
    }

    public function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }

        parent::tearDown();
    }
}
