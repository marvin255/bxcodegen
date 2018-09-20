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
        $pathManager->setAlias($aliasName, $aliasPath);

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
}
