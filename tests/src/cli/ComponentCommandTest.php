<?php

namespace marvin255\bxcodegen\tests\cli;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\cli\ComponentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComponentCommandTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $componentName = 'component_' . mt_rand();
        $componentTitle = 'title_' . mt_rand();

        $codegen = $this->getMockBuilder(Bxcodegen::class)
           ->disableOriginalConstructor()
           ->getMock();
        $codegen->expects($this->once())
            ->method('run')
            ->with(
                $this->equalTo('component'),
                $this->callback(function ($options) use ($componentName, $componentTitle) {
                    return $options->get('name') === $componentName
                        && $options->get('title') === $componentTitle;
                })
            );

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->with($this->equalTo('name'))->will($this->returnValue($componentName));
        $input->method('getOption')->with($this->equalTo('title'))->will($this->returnValue($componentTitle));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new ComponentCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);

        $this->assertSame('bxcodegen:component', $command->getName());
    }
}
