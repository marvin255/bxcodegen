<?php

namespace marvin255\bxcodegen;

use marvin255\bxcodegen\service\options\Collection;
use marvin255\bxcodegen\service\yaml\SymfonyYaml;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\filesystem\Copier;
use InvalidArgumentException;

/**
 * Фабрика, которая создает и настраивает объект Bxcodegen.
 */
class Factory
{
    /**
     * Создает объект Bxcodegen из настроек в yaml файле.
     *
     * @param string $pathToYaml
     *
     * @return \marvin255\bxcodegen\Bxcodegen
     *
     * @throws \InvalidArgumentException
     */
    public static function createCodegenFromYaml($pathToYaml)
    {
        if (!file_exists($pathToYaml)) {
            throw new InvalidArgumentException(
                "Can't find yaml with settings: {$pathToYaml}"
            );
        }

        $rootFolder = pathinfo($pathToYaml, PATHINFO_DIRNAME);
        $defaultOption = [
            'services' => [
                'pathManager' => [
                    PathManager::class,
                    $rootFolder,
                    [
                        'components' => '/web/local/components',
                        'modules' => '/web/local/modules',
                    ],
                ],
                'twig' => [
                    Twig::class,
                ],
                'copier' => [
                    Copier::class,
                ],
            ],
        ];
        $optionsFromYaml = (new SymfonyYaml)->parseFromFile($pathToYaml);
        $options = new Collection(array_merge_recursive($defaultOption, $optionsFromYaml));
        $locator = new ServiceLocator;

        return new Bxcodegen($options, $locator);
    }
}
