<?php

namespace marvin255\bxcodegen\tests\generator;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\generator\Module;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\service\options\Collection;
use InvalidArgumentException;

class ModuleTest extends BaseCase
{
    /**
     * @var string
     */
    protected $folderPath;

    /**
     * @test
     */
    public function testRun()
    {
        $moduleName = 'vendor.name_' . mt_rand();

        $options = new Collection([
            'name' => $moduleName,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager(dirname($this->folderPath), [
            'modules' => 'modules',
        ]));

        $generator = new Module;
        $generator->generate($options, $locator);

        $classFile = "{$this->folderPath}/{$moduleName}/install/index.php";
        $this->assertFileExists($classFile);
        $this->assertContains($moduleName, file_get_contents($classFile));
    }

    /**
     * @test
     */
    public function testRunEmptyNameException()
    {
        $options = new Collection([]);
        $locator = new ServiceLocator;

        $generator = new Module;

        $this->setExpectedException(InvalidArgumentException::class);
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testRunWrongNameException()
    {
        $name = 'name/space.component' . mt_rand();
        $options = new Collection(['name' => $name]);
        $locator = new ServiceLocator;

        $generator = new Module;

        $this->setExpectedException(InvalidArgumentException::class, $name);
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testRunDestinationExistsException()
    {
        $moduleName = 'vendor.name_' . mt_rand();

        $options = new Collection([
            'name' => $moduleName,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager(dirname($this->folderPath), [
            'modules' => 'modules',
        ]));

        $generator = new Module;
        $generator->generate($options, $locator);

        $this->setExpectedException(InvalidArgumentException::class, $moduleName);
        $generator->generate($options, $locator);
    }

    /**
     * Подготоваливает директорию для тестов.
     *
     * @throws \RuntimeException
     */
    public function setUp()
    {
        $this->folderPath = sys_get_temp_dir() . '/modules';
        if (!mkdir($this->folderPath)) {
            throw new RuntimeException(
                "Can't create {$this->folderPath} folder"
            );
        }

        parent::setUp();
    }

    /**
     * Удаляет тестовую директорию и все ее содержимое.
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

        parent::tearDown();
    }
}