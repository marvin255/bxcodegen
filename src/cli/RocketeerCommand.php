<?php

namespace marvin255\bxcodegen\cli;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use marvin255\bxcodegen\service\options\Collection;

/**
 * Консольная команда для Symfony console, которая запускает
 * генератор конфигурации для rocketeer.
 */
class RocketeerCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxcodegen:rocketeer')
            ->setDescription('Create rocketeer configuration for bitrix')
            ->addOption(
                'application',
                'a',
                InputOption::VALUE_REQUIRED,
                'Application name'
            )
            ->addOption(
                'root',
                'r',
                InputOption::VALUE_REQUIRED,
                'Root folder on target server'
            )
            ->addOption(
                'repository',
                's',
                InputOption::VALUE_REQUIRED,
                'Url for repo'
            )
            ->addOption(
                'host',
                'h',
                InputOption::VALUE_REQUIRED,
                'Host for target server'
            )
            ->addOption(
                'username',
                'u',
                InputOption::VALUE_OPTIONAL,
                'User name for target server'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_OPTIONAL,
                'User password name for target server'
            )
            ->addOption(
                'branch',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Branch in repo for deploy'
            );
    }

    /**
     * @inheritdoc
     */
    protected function collectGeneratorNameFromInput(InputInterface $input)
    {
        return 'rocketeer';
    }

    /**
     * @inheritdoc
     */
    protected function collectOptionsFromInput(InputInterface $input)
    {
        $return = [
            'application_name' => $input->getArgument('application'),
            'root_directory' => $input->getArgument('root'),
            'repository' => $input->getArgument('repository'),
            'branch' => $input->getArgument('branch'),
            'host' => $input->getArgument('host'),
            'username' => $input->getArgument('username'),
            'password' => $input->getArgument('password'),
            'gitignore_inject' => true,
            'phar_inject' => true,
        ];

        return new Collection($return);
    }
}
