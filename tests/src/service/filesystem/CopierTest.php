<?php

namespace marvin255\bxcodegen\tests\service\filesystem;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\service\filesystem\FileInterface;
use InvalidArgumentException;

class CopierTest extends BaseCase
{
    /**
     * @var string
     */
    protected $sourceFolderPath;
    /**
     * @var string
     */
    protected $sourceFolderChildren = [];
    /**
     * @var string
     */
    protected $destinationFolderPath;

    /**
     * @test
     */
    public function testCopyDir()
    {
        $source = new Directory($this->sourceFolderPath);
        $destination = new Directory($this->destinationFolderPath);

        $copier = new Copier;
        $copier->copyDir($source, $destination);

        foreach ($this->sourceFolderChildren as $sourceFile => $destinationFile) {
            $this->assertFileExists($destinationFile);
            $this->assertFileEquals($sourceFile, $destinationFile);
        }
    }

    /**
     * @test
     */
    public function testCopyDirWithTransformer()
    {
        $source = new Directory($this->sourceFolderPath);
        $destination = new Directory($this->destinationFolderPath);
        $tranformedContent = 'transformed_content_' . mt_rand();
        $transformer = function ($from, $to) use ($tranformedContent) {
            $return = false;
            if ($from instanceof FileInterface && $to instanceof FileInterface) {
                file_put_contents($to->getPathname(), $tranformedContent);
                $return = true;
            }

            return $return;
        };

        $copier = new Copier;
        $copier->addTransformer($transformer);
        $copier->copyDir($source, $destination);

        foreach ($this->sourceFolderChildren as $sourceFile => $destinationFile) {
            $this->assertFileExists($destinationFile);
            $this->assertFileNotEquals($sourceFile, $destinationFile);
            $this->assertSame($tranformedContent, file_get_contents($destinationFile));
        }
    }

    /**
     * @test
     */
    public function testAddTransformerException()
    {
        $copier = new Copier;

        $this->expectException(InvalidArgumentException::class);
        $copier->addTransformer(true);
    }

    /**
     * @test
     */
    public function testClearTransformers()
    {
        $source = new Directory($this->sourceFolderPath);
        $destination = new Directory($this->destinationFolderPath);
        $transformer = function ($from, $to) {
            throw new InvalidArgumentException('Transfromer must be cleared');
        };

        $copier = new Copier;
        $copier->addTransformer($transformer)->clearTransformers();
        $copier->copyDir($source, $destination);

        foreach ($this->sourceFolderChildren as $sourceFile => $destinationFile) {
            $this->assertFileExists($destinationFile);
            $this->assertFileEquals($sourceFile, $destinationFile);
        }
    }

    /**
     * Подготоваливает директории для копирования.
     */
    public function setUp()
    {
        //исходная папка
        $this->sourceFolderPath = sys_get_temp_dir() . '/source_folder_' . mt_rand();
        if (!mkdir($this->sourceFolderPath, 0777, true)) {
            throw new InvalidArgumentException(
                "Can't create source folder"
            );
        }

        //папка, в которую будет произведено копирование
        $this->destinationFolderPath = sys_get_temp_dir() . '/destination_folder_' . mt_rand();

        //вложенная папка с файлом
        $subFolder = $this->sourceFolderPath
            . '/sub_1_' . mt_rand()
            . '/sub_2_' . mt_rand();
        if (!mkdir($subFolder, 0777, true)) {
            throw new InvalidArgumentException(
                "Can't create sub folder"
            );
        }
        $subFile = $subFolder . '/file_' . mt_rand() . '.test';
        file_put_contents($subFile, 'sub_file_content_' . mt_rand());
        $subFileDest = str_replace($this->sourceFolderPath, $this->destinationFolderPath, $subFile);
        $this->sourceFolderChildren[$subFile] = $subFileDest;

        //вложенный файл
        $subFile = $this->sourceFolderPath . '/file_' . mt_rand() . '.test';
        file_put_contents($subFile, 'sub_file_content_' . mt_rand());
        $subFileDest = str_replace($this->sourceFolderPath, $this->destinationFolderPath, $subFile);
        $this->sourceFolderChildren[$subFile] = $subFileDest;

        parent::setUp();
    }

    /**
     * Удаляет тестовую директорию и все е содержимое.
     */
    public function tearDown()
    {
        $this->removeDir($this->sourceFolderPath);
        $this->removeDir($this->destinationFolderPath);

        parent::tearDown();
    }
}
