<?php

namespace marvin255\bxcodegen\tests\services\filesystem;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\services\filesystem\Copier;
use marvin255\bxcodegen\services\filesystem\Directory;
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
        self::rmdir($this->sourceFolderPath);
        self::rmdir($this->destinationFolderPath);

        parent::tearDown();
    }

    /**
     * Удаляет папку со всеми вложенными элементами.
     *
     * @param string $folderPath
     */
    protected static function rmdir($folderPath)
    {
        if (is_dir($folderPath)) {
            $objects = scandir($folderPath);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($folderPath . '/' . $object)) {
                        self::rmdir($folderPath . '/' . $object);
                    } else {
                        unlink($folderPath . '/' . $object);
                    }
                }
            }
            rmdir($folderPath);
        }
    }
}
