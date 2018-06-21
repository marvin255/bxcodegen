<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\services\renderer\Twig;
use marvin255\bxcodegen\services\options\CollectionInterface;
use marvin255\bxcodegen\Exception;
use Twig_Loader_String;
use Twig_Loader_Filesystem;
use Twig_Environment;

class TwigTest extends BaseCase
{
    /**
     * @test
     */
    public function testRender()
    {
        $twigLoader = new Twig_Loader_String;
        $twig = new Twig_Environment($twigLoader, ['cache' => false]);
        $arOptions = ['test_param' => 'test_param_value'];
        $options = $this->getMockBuilder(CollectionInterface::class)->getMock();
        $options->method('getAll')->will($this->returnValue($arOptions));

        $renderer = new Twig($twig);
        $res = $renderer->render(__DIR__ . '/_fixture/template.twig', $options);
        $expected = file_get_contents(__DIR__ . '/_fixture/expected.txt');

        $this->assertSame($expected, $res);
    }

    /**
     * @test
     */
    public function testRenderException()
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/_fixture/');
        $twig = new Twig_Environment($twigLoader, ['cache' => false]);
        $options = $this->getMockBuilder(CollectionInterface::class)->getMock();

        $renderer = new Twig($twig);

        $this->setExpectedException(Exception::class);
        $res = $renderer->render('unexisted_template.twig', $options);
    }
}
