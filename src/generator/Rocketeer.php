<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор для создания конфигурационных файлов Rocketeer.
 */
class Rocketeer extends AbstractGenerator
{
    /**
     * @var string
     */
    protected $rocketeerFolderName = '.rocketeer';

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $templateData = $this->collectDataFromInputForTemplate($options);
        $sourcePath = $options->get('source', dirname(dirname(__DIR__)) . '/templates/rocketeer');
        $destinationPath = $locator->get('pathManager')->getAbsolutePath("/{$this->rocketeerFolderName}");

        $copier = $this->getAndConfigurateCopierFromLocator($locator, $templateData);
        $source = new Directory($sourcePath);
        $destination = new Directory($destinationPath);

        if ($destination->isExists()) {
            throw new InvalidArgumentException(
                'Directory ' . $destination->getPathname() . ' already exists'
            );
        }

        $copier->copyDir($source, $destination);
    }

    /**
     * Собирает массив опций для шаблонов из тех опций, что пришли от пользователя.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $options
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function collectDataFromInputForTemplate(CollectionInterface $options)
    {
        return [
            'application_name' => $options->get('application_name', ''),
            'root_directory' => $options->get('root_directory', ''),
            'repository' => $options->get('repository', ''),
            'branch' => $options->get('branch', 'master'),
            'username' => $options->get('username', ''),
            'password' => $options->get('password', ''),
            'key' => $options->get('key', ''),
            'keyphrase' => $options->get('keyphrase', ''),
        ];
    }
}
