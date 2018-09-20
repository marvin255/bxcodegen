<?php

namespace marvin255\bxcodegen\cli;

use marvin255\bxcodegen\Bxcodegen;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use marvin255\bxcodegen\service\options\Collection;
use Exception;

/**
 * Консольная команда для Symfony console, которая запускает указанный
 * кастомный генератор.
 */
class GeneratorCommand extends Command
{
    /**
     * @var \marvin255\bxcodegen\Bxcodegen
     */
    protected $bxcodegen;

    /**
     * Задает объект, который управляет генераторами кода.
     *
     * @param \marvin255\bxcodegen\Bxcodegen $bxcodegen
     *
     * @return self
     */
    public function setBxcodegen(Bxcodegen $bxcodegen)
    {
        $this->bxcodegen = $bxcodegen;

        return $this;
    }

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
            )
            ->addOption(
                'option',
                'o',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Options that will be passed to generator in format optionName=optionValue'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generatorName = $this->collectGeneratorNameFromInput($input);
        $generatorOptions = $this->collectOptionsFromInput($input);

        $output->writeln("<info>Starting {$generatorName} generator:</info>");

        try {
            $this->bxcodegen->run($generatorName, $generatorOptions);
            $output->writeln("<info>    - {$generatorName} generator completed</info>");
        } catch (Exception $e) {
            $msg = $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            $output->writeln(
                "<error>    - {$generatorName} generator failed: {$msg}</error>"
            );
        }
    }

    /**
     * Возвращает имя генератора, который нужно использовать для команды.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return string
     */
    protected function collectGeneratorNameFromInput(InputInterface $input)
    {
        return $input->getArgument('name');
    }

    /**
     * Получает массив настроек генератора из объекта InputInterface.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \marvin255\bxcodegen\service\options\Collection
     */
    protected function collectOptionsFromInput(InputInterface $input)
    {
        $return = [];
        $rawOptions = $input->getOption('option');

        foreach ($rawOptions as $rawOption) {
            list($name, $value) = explode('=', $rawOption, 2);
            $return[$name] = $value;
        }

        return new Collection($return);
    }
}
