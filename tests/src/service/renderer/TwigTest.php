<?php

namespace marvin255\bxcodegen\tests\service\renderer;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\Exception;

class TwigTest extends BaseCase
{
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

        $this->setExpectedException(Exception::class);
        $res = $renderer->renderTemplate(__DIR__ . '/_fixture/unexisted.twig');
    }
}
