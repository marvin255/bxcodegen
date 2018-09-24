<?php

namespace marvin255\bxcodegen\tests\generator;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\generator\Orm;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\service\options\Collection;
use InvalidArgumentException;

class OrmTest extends BaseCase
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
        $table = 'b_table_' . mt_rand();
        $namespace = 'Test\\Namespace' . mt_rand();
        $class = $namespace . '\\ModelTable';

        $options = new Collection([
            'table' => $table,
            'class' => $class,
            'create_query' => true,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            $namespace => 'test',
        ]));

        $generator = new Orm;
        $generator->generate($options, $locator);

        $modelFile = "{$this->folderPath}/test/modeltable.php";
        $this->assertFileExists($modelFile);
        $this->assertContains($namespace, file_get_contents($modelFile));
        $this->assertContains($table, file_get_contents($modelFile));
        $this->assertContains('ModelQuery', file_get_contents($modelFile));

        $queryFile = "{$this->folderPath}/test/modelquery.php";
        $this->assertFileExists($queryFile);
        $this->assertContains($namespace, file_get_contents($queryFile));
    }

    /**
     * @test
     */
    public function testRunNoQuery()
    {
        $table = 'b_table_' . mt_rand();
        $namespace = 'Test\\Namespace' . mt_rand();
        $class = $namespace . '\\ModelTable';

        $options = new Collection([
            'table' => $table,
            'class' => $class,
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            $namespace => 'test',
        ]));

        $generator = new Orm;
        $generator->generate($options, $locator);

        $modelFile = "{$this->folderPath}/test/modeltable.php";
        $this->assertFileExists($modelFile);
        $this->assertContains($namespace, file_get_contents($modelFile));
        $this->assertContains($table, file_get_contents($modelFile));
        $this->assertNotContains('ModelQuery', file_get_contents($modelFile));

        $queryFile = "{$this->folderPath}/test/modelquery.php";
        $this->assertFileNotExists($queryFile);
    }

    /**
     * @test
     */
    public function testAlreadyExistsException()
    {
        $options = new Collection([
            'table' => 'table',
            'class' => '\\Test\\ModelTable',
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            '\\Test' => 'test',
        ]));

        $generator = new Orm;
        $generator->generate($options, $locator);

        $this->expectException(InvalidArgumentException::class);
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testRunEmptyClassPathException()
    {
        $table = 'b_table_' . mt_rand();
        $namespace = 'Test\\Namespace' . mt_rand();
        $class = $namespace . '\\ModelTable';

        $options = new Collection([
            'table' => $table,
            'class' => $class,
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath));

        $generator = new Orm;

        $this->expectException(InvalidArgumentException::class, $class);
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testEmptyTableException()
    {
        $options = new Collection([
            'table' => false,
            'class' => '\\Test\\ModelTable',
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            '\\Test\\ModelTable' => 'test',
        ]));

        $generator = new Orm;

        $this->expectException(InvalidArgumentException::class, 'table');
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testWrongTableException()
    {
        $options = new Collection([
            'table' => 'test test',
            'class' => '\\Test\\ModelTable',
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            '\\Test\\ModelTable' => 'test',
        ]));

        $generator = new Orm;

        $this->expectException(InvalidArgumentException::class, 'test test');
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testEmptyClassException()
    {
        $options = new Collection([
            'table' => 'table',
            'class' => null,
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            '\\Test\\ModelTable' => 'test',
        ]));

        $generator = new Orm;

        $this->expectException(InvalidArgumentException::class, 'class');
        $generator->generate($options, $locator);
    }

    /**
     * @test
     */
    public function testWrongClassException()
    {
        $options = new Collection([
            'table' => 'test_test',
            'class' => '\\Test\\Model',
            'create_query' => false,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath, [], [
            '\\Test\\ModelTable' => 'test',
        ]));

        $generator = new Orm;

        $this->expectException(InvalidArgumentException::class, '\\Test\\Model');
        $generator->generate($options, $locator);
    }

    /**
     * Подготоваливает директорию для тестов.
     *
     * @throws \RuntimeException
     */
    public function setUp()
    {
        $this->folderPath = $this->getTempFolder();

        parent::setUp();
    }
}
