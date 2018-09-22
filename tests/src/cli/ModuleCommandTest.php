<?php

namespace marvin255\bxcodegen\tests\cli;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\cli\ModuleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

class ModuleCommandTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $moduleName = 'component_' . mt_rand();
        $moduleTitle = 'title_' . mt_rand();

        $codegen = $this->getMockBuilder(Bxcodegen::class)
           ->disableOriginalConstructor()
           ->getMock();
        $codegen->expects($this->once())
            ->method('run')
            ->with(
                $this->equalTo('module'),
                $this->callback(function ($options) use ($moduleName, $moduleTitle) {
                    return $options->get('name') === $moduleName
                        && $options->get('title') === $moduleTitle
                        && $options->get('options') === false;
                })
            );

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->with($this->equalTo('name'))->will($this->returnValue($moduleName));
        $input->method('getOption')->will($this->returnCallback(function ($option) use ($moduleTitle) {
            $options = ['title' => $moduleTitle, 'no-options' => true];

            return isset($options[$option]) ? $options[$option] : null;
        }));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new ModuleCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);

        $this->assertSame('bxcodegen:module', $command->getName());
    }

    /**
     * @test
     */
    public function testExecuteException()
    {
        $message = 'message_' . mt_rand();

        $codegen = $this->getMockBuilder(Bxcodegen::class)
           ->disableOriginalConstructor()
           ->getMock();
        $codegen->method('run')->will($this->throwException(new InvalidArgumentException($message)));

        $input = $this->getMockBuilder(InputInterface::class)->getMock();

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->expects($this->at(1))
            ->method('writeln')
            ->with($this->stringContains($message));

        $command = new ModuleCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);
    }
}
