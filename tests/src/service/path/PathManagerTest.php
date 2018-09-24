<?php

namespace marvin255\bxcodegen\tests\service\options;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\path\PathManager;
use InvalidArgumentException;

class PathManagerTest extends BaseCase
{
    /**
     * @test
     */
    public function testWrongPathInConstructorException()
    {
        $emptyPath = 'empty_path_' . mt_rand();

        $this->setExpectedException(InvalidArgumentException::class, $emptyPath);
        $pathManager = new PathManager($emptyPath);
    }

    /**
     * @test
     */
    public function testGetAbsolutePath()
    {
        $relativePath = '/dir/test.txt';
        $absolutePath = __DIR__ . '/_fixture/dir/test.txt';

        $relativePath2 = '..\path\dir\test.txt';
        $absolutePath2 = __DIR__ . '/_fixture/../path/dir/test.txt';

        $pathManager = new PathManager(__DIR__ . '/_fixture');

        $this->assertSame($absolutePath, $pathManager->getAbsolutePath($relativePath));
        $this->assertSame($absolutePath2, $pathManager->getAbsolutePath($relativePath2));
    }

    /**
     * @test
     */
    public function testSetAlias()
    {
        $aliasName = 'alias_name_' . mt_rand();
        $aliasPath = '/dir/';
        $absolutePath = __DIR__ . '/_fixture/dir/test.txt';

        $pathManager = new PathManager(__DIR__ . '/_fixture');

        $this->assertSame(
            $pathManager,
            $pathManager->setAlias($aliasName, $aliasPath)
        );
        $this->assertSame(
            $absolutePath,
            $pathManager->getAbsolutePath("@{$aliasName}/test.txt")
        );
    }

    /**
     * @test
     */
    public function testSetAliasNameException()
    {
        $aliasName = 'alias name ' . mt_rand();
        $pathManager = new PathManager(__DIR__ . '/_fixture');

        $this->setExpectedException(InvalidArgumentException::class, $aliasName);
        $pathManager->setAlias($aliasName, '/dir/');
    }

    /**
     * @test
     */
    public function testAliasesInConstructor()
    {
        $aliasName = 'alias_name_' . mt_rand();
        $aliasPath = '/dir/';
        $absolutePath = __DIR__ . '/_fixture/dir/test.txt';

        $pathManager = new PathManager(__DIR__ . '/_fixture', [
            $aliasName => $aliasPath,
        ]);

        $this->assertSame(
            $absolutePath,
            $pathManager->getAbsolutePath("@{$aliasName}/test.txt")
        );
    }

    /**
     * @test
     */
    public function testGetAbsolutePathForClass()
    {
        $rootDir = __DIR__ . '/_fixture';
        $lib = 'lib_' . mt_rand();
        $rootNamespace = 'Root\\Namespace' . mt_rand();

        $testClass = "\\{$rootNamespace}\\ItemClass";
        $testPath = "{$rootDir}/{$lib}/itemclass.php";

        $testClass1 = "{$rootNamespace}\\folder\\subfolder\\ItemClass";
        $testPath1 = "{$rootDir}/{$lib}/folder/subfolder/itemclass.php";

        $moduleClass = 'Vendor\\Module\\folder\\subfolder\\ItemClass';
        $modulePath = "{$rootDir}/modules/vendor.module/lib/folder/subfolder/itemclass.php";

        $pathManager = new PathManager(
            $rootDir,
            ['lib' => $lib, 'modules' => 'modules'],
            [$rootNamespace => '@lib']
        );

        $this->assertSame(
            $testPath,
            $pathManager->getAbsolutePathForClass($testClass)
        );
        $this->assertSame(
            $testPath1,
            $pathManager->getAbsolutePathForClass($testClass1)
        );
        $this->assertSame(
            $modulePath,
            $pathManager->getAbsolutePathForClass($moduleClass)
        );
    }

    /*
     * @test
     */
    public function testSetPathForNamespace()
    {
        $rootDir = __DIR__ . '/_fixture';
        $lib = 'lib_' . mt_rand();
        $rootNamespace = 'Root\\Namspace' . mt_rand();

        $testClass = "{$rootNamespace}\\ItemClass";
        $testPath = "{$rootDir}/{$lib}/itemclass.php";

        $pathManager = new PathManager($rootDir);

        $this->assertSame(
            $pathManager,
            $pathManager->setPathForNamespace($rootNamespace, "/{$lib}/")
        );
        $this->assertSame(
            $testPath,
            $pathManager->getAbsolutePathForClass($testClass)
        );
        $this->assertSame(
            null,
            $pathManager->getAbsolutePathForClass('\\Empty\\Namespace\\Item')
        );
    }

    /*
     * @test
     */
    public function testSetPathForNamespaceException()
    {
        $rootDir = __DIR__ . '/_fixture';
        $namespace = '$123-test-' . mt_rand();

        $pathManager = new PathManager($rootDir);

        $this->setExpectedException(InvalidArgumentException::class, $namespace);
        $pathManager->setPathForNamespace($namespace, '/test/');
    }
}
