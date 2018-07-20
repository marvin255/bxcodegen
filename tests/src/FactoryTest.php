<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\cli\GeneratorCommand;
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
        $app->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(GeneratorCommand::class));

        $res = Factory::registerCommands($app, __DIR__ . '/_fixture/options.yaml');

        $this->assertInstanceOf(Application::class, $res);
    }

    /**
     * @test
     */
    public function testRegisterCommandsUnexistedFileException()
    {
        $pathToYaml = __DIR__ . '/_fixture/no_options.yaml';
        $app = $this->getMockBuilder(Application::class)
           ->disableOriginalConstructor()
           ->getMock();

        $this->setExpectedException(InvalidArgumentException::class, $pathToYaml);
        Factory::registerCommands($app, $pathToYaml);
    }
}
