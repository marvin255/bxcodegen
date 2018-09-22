<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\cli\ComponentCommand;
use marvin255\bxcodegen\cli\ModuleCommand;
use marvin255\bxcodegen\cli\RocketeerCommand;
use marvin255\bxcodegen\Factory;
use Symfony\Component\Console\Application;
use InvalidArgumentException;

class FactoryTest extends BaseCase
{
    /**
     * @test
     */
    public function testRegisterCommands()
    {
        $app = $this->getMockBuilder(Application::class)
           ->disableOriginalConstructor()
           ->getMock();

        $app->expects($this->at(0))
            ->method('add')
            ->with($this->isInstanceOf(ComponentCommand::class));
        $app->expects($this->at(1))
            ->method('add')
            ->with($this->isInstanceOf(ModuleCommand::class));
        $app->expects($this->at(2))
            ->method('add')
            ->with($this->isInstanceOf(RocketeerCommand::class));

        $fixturesFolder = __DIR__ . '/_fixture';
        $codegen = Factory::registerCommands($app, $fixturesFolder . '/options.yaml');

        $this->assertInstanceOf(Bxcodegen::class, $codegen);
        $this->assertSame($fixturesFolder, $codegen->getOptions()->get('test_option_replace'));
        $this->assertSame(
            ['test_nested_test' => 'test', 'test_nested_replace' => $fixturesFolder],
            $codegen->getOptions()->get('test_nested')
        );
    }

    /**
     * @test
     */
    public function testRegisterCommandsEmptyYamlException()
    {
        $app = $this->getMockBuilder(Application::class)
           ->disableOriginalConstructor()
           ->getMock();

        $this->setExpectedException(InvalidArgumentException::class);
        Factory::registerCommands($app, __DIR__ . '/_fixture/no_options.yaml');
    }

    /**
     * @test
     */
    public function testCreateDefault()
    {
        $res = Factory::createDefault(__DIR__ . '/_fixture/');

        $this->assertInstanceOf(Bxcodegen::class, $res);
    }
}
