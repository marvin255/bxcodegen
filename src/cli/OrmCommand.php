<?php

namespace marvin255\bxcodegen\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use marvin255\bxcodegen\service\options\Collection;

/**
 * Консольная команда для Symfony console, которая запускает
 * генератор orm.
 */
class OrmCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxcodegen:orm')
            ->setDescription('Create orm item for bitrix')
            ->addArgument(
                'table',
                InputArgument::REQUIRED,
                'Table name for orm'
            )
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                'Class name for orm'
            )
            ->addOption(
                'create-query',
                null,
                InputOption::VALUE_OPTIONAL,
                'Create class for orm query',
                false
            );
    }

    /**
     * @inheritdoc
     */
    protected function collectGeneratorNameFromInput(InputInterface $input)
    {
        return 'orm';
    }

    /**
     * @inheritdoc
     */
    protected function collectOptionsFromInput(InputInterface $input)
    {
        $return = [
            'table' => $input->getArgument('table'),
            'class' => $input->getArgument('class'),
            'create_query' => $input->getOption('create-query'),
        ];

        return new Collection($return);
    }
}
