<?php

namespace marvin255\bxcodegen\tests\cli;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\cli\OrmCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

class OrmCommandTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $options = [
            'table' => 'table_' . mt_rand(),
            'class' => '\\Vendor\\Model' . mt_rand() . 'Table',
            'create-query' => true,
        ];
        $commandOptions = [
            'table' => $options['table'],
            'class' => $options['class'],
            'create_query' => $options['create-query'],
        ];

        $codegen = $this->getMockBuilder(Bxcodegen::class)
           ->disableOriginalConstructor()
           ->getMock();
        $codegen->expects($this->once())
            ->method('run')
            ->with(
                $this->equalTo('orm'),
                $this->callback(function ($command) use ($commandOptions) {
                    $res = true;
                    foreach ($commandOptions as $name => $value) {
                        if ($command->get($name) !== $value) {
                            $res = false;
                            break;
                        }
                    }

                    return $res;
                })
            );

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->will($this->returnCallback(function ($argument) use ($options) {
            return isset($options[$argument]) ? $options[$argument] : null;
        }));
        $input->method('getOption')->will($this->returnCallback(function ($option) use ($options) {
            return isset($options[$option]) ? $options[$option] : null;
        }));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new OrmCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);

        $this->assertSame('bxcodegen:orm', $command->getName());
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

        $command = new OrmCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);
    }
}
