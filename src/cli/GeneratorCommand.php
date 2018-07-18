<?php

namespace marvin255\bxcodegen\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Консольная команда для Symfony console, которая создает новую миграцию.
 */
class GeneratorCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxcodegen:generate')
            ->setDescription('Create code entity for bitrix')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of generator to run'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}