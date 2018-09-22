<?php

namespace marvin255\bxcodegen\tests\cli;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\cli\RocketeerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

class RocketeerCommandTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $options = [
            'application' => 'application-' . mt_rand(),
            'root' => 'root-' . mt_rand(),
            'repository' => 'repository-' . mt_rand(),
            'host' => 'host-' . mt_rand(),
            'username' => 'username-' . mt_rand(),
            'password' => 'password-' . mt_rand(),
            'branch' => 'branch-' . mt_rand(),
        ];
        $commandOptions = [
            'application_name' => $options['application'],
            'root_directory' => $options['root'],
            'repository' => $options['repository'],
            'host' => $options['host'],
            'username' => $options['username'],
            'password' => $options['password'],
            'branch' => $options['branch'],
            'gitignore_inject' => true,
            'phar_inject' => true,
        ];

        $codegen = $this->getMockBuilder(Bxcodegen::class)
           ->disableOriginalConstructor()
           ->getMock();
        $codegen->expects($this->once())
            ->method('run')
            ->with(
                $this->equalTo('rocketeer'),
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
        $input->method('getOption')->will($this->returnCallback(function ($option) use ($options) {
            return isset($options[$option]) ? $options[$option] : null;
        }));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new RocketeerCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);

        $this->assertSame('bxcodegen:rocketeer', $command->getName());
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

        $command = new RocketeerCommand;
        $command->setBxcodegen($codegen);
        $command->run($input, $output);
    }
}
