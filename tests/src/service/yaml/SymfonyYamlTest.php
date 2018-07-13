<?php

namespace marvin255\bxcodegen\tests\service\yaml;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\yaml\SymfonyYaml;
use marvin255\bxcodegen\Exception;

class SymfonyYamlTest extends BaseCase
{
    /**
     * @test
     */
    public function testParseFromFile()
    {
        $reader = new SymfonyYaml;

        $readedResult = $reader->parseFromFile(__DIR__ . '/_fixture/yaml.yaml');
        $expectedResult = include __DIR__ . '/_fixture/yaml_expected.php';

        $this->assertSame($expectedResult, $readedResult);
    }

    /**
     * @test
     */
    public function testParseFromFileUnexistedFileException()
    {
        $reader = new SymfonyYaml;

        $this->setExpectedException(Exception::class);
        $reader->parseFromFile(__DIR__ . '/_fixture/unexisted.yaml');
    }

    /**
     * @test
     */
    public function testParseFromFileParseErrorException()
    {
        $reader = new SymfonyYaml;

        $this->setExpectedException(Exception::class);
        $reader->parseFromFile(__DIR__ . '/_fixture/yaml_broken.yaml');
    }
}
