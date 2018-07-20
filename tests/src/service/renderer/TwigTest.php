<?php

namespace marvin255\bxcodegen\tests\service\renderer;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\Exception;
use InvalidArgumentException;

class TwigTest extends BaseCase
{
    /**
     * @var string
     */
    protected $tempFile;

    /**
     * @test
     */
    public function testRenderTemplate()
    {
        $options = ['test_param' => 'test_param_value'];

        $renderer = new Twig;
        $rendered = $renderer->renderTemplate(__DIR__ . '/_fixture/template.twig', $options);
        $expected = file_get_contents(__DIR__ . '/_fixture/expected.txt');

        $this->assertSame($expected, $rendered);
    }

    /**
     * @test
     */
    public function testRenderTemplateTwigException()
    {
        $renderer = new Twig;

        $this->setExpectedException(Exception::class);
        $res = $renderer->renderTemplate(__DIR__ . '/_fixture/template_exception.twig');
    }

    /**
     * @test
     */
    public function testRenderTemplateUnexistedFileException()
    {
        $renderer = new Twig;

        $this->setExpectedException(InvalidArgumentException::class);
        $res = $renderer->renderTemplate(__DIR__ . '/_fixture/unexisted.twig');
    }

    /**
     * @test
     */
    public function testRenderTemplateToFile()
    {
        $options = ['test_param' => 'test_param_value'];

        $renderer = new Twig;
        $renderer->renderTemplateToFile(
            __DIR__ . '/_fixture/template.twig',
            $this->tempFile,
            $options
        );
        $expected = file_get_contents(__DIR__ . '/_fixture/expected.txt');
        $actual = file_get_contents($this->tempFile);

        $this->assertSame($expected, $actual);
    }

    /**
     * Создает тестовый файл и подготавливает массив с информацией о нем.
     */
    public function setUp()
    {
        $this->tempFile = sys_get_temp_dir() . '/test.test';

        parent::setUp();
    }

    /**
     * Удаляет тестовый файл.
     */
    public function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }

        parent::tearDown();
    }
}
