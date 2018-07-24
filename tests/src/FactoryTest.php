<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\cli\GeneratorCommand;
use marvin255\bxcodegen\cli\ComponentCommand;
use marvin255\bxcodegen\cli\ModuleCommand;
use marvin255\bxcodegen\Factory;
use Symfony\Component\Console\Application;

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
            ->with($this->isInstanceOf(GeneratorCommand::class));
        $app->expects($this->at(1))
            ->method('add')
            ->with($this->isInstanceOf(ComponentCommand::class));
        $app->expects($this->at(2))
            ->method('add')
            ->with($this->isInstanceOf(ModuleCommand::class));

        $res = Factory::registerCommands($app, __DIR__ . '/_fixture/options.yaml');

        $this->assertInstanceOf(Application::class, $res);
    }
}
