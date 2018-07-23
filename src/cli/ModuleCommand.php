<?php

namespace marvin255\bxcodegen\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use marvin255\bxcodegen\service\options\Collection;

/**
 * Консольная команда для Symfony console, которая запускает указанный
 * генератор модуля.
 */
class ModuleCommand extends GeneratorCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxcodegen:module')
            ->setDescription('Create module for bitrix')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of module vendor.name'
            )
            ->addOption(
                'title',
                't',
                InputOption::VALUE_REQUIRED,
                'Readable title for module'
            );
    }

    /**
     * @inheritdoc
     */
    protected function collectGeneratorNameFromInput(InputInterface $input)
    {
        return 'module';
    }

    /**
     * @inheritdoc
     */
    protected function collectOptionsFromInput(InputInterface $input)
    {
        $return = [
            'name' => $input->getArgument('name'),
        ];

        if ($input->getOption('title') !== null) {
            $return['title'] = $input->getOption('title');
        }

        return new Collection($return);
    }
}
