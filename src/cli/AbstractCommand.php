<?php

namespace marvin255\bxcodegen\cli;

use marvin255\bxcodegen\Bxcodegen;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Exception;

/**
 * Абстрактная консольная команда для генератора.
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var \marvin255\bxcodegen\Bxcodegen
     */
    protected $bxcodegen;

    /**
     * Возвращает имя генератора, который нужно использовать для команды.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return string
     */
    abstract protected function collectGeneratorNameFromInput(InputInterface $input);

    /**
     * Получает массив настроек генератора из объекта InputInterface.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \marvin255\bxcodegen\service\options\Collection
     */
    abstract protected function collectOptionsFromInput(InputInterface $input);

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
}
