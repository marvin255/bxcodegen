<?php

namespace marvin255\bxcodegen;

use marvin255\bxcodegen\service\options\Collection;
use marvin255\bxcodegen\service\yaml\SymfonyYaml;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\cli\GeneratorCommand;
use Symfony\Component\Console\Application;
use InvalidArgumentException;

/**
 * Фабрика, которая создает и настраивает объект Bxcodegen.
 */
class Factory
{
    /**
     * Регистрирует консольные команды в объекте приложения Symfony console,
     * из настроек, указанных в yaml файле.
     *
     * @param \Symfony\Component\Console\Application $app
     * @param string                                 $pathToYaml
     *
     * @return \Symfony\Component\Console\Application
     */
    public static function registerCommands(Application $app, $pathToYaml)
    {
        $bxcodegen = self::createCodegenFromYaml($pathToYaml);

        $app->add((new GeneratorCommand)->setBxcodegen($bxcodegen));

        return $app;
    }

    /**
     * Создает объект Bxcodegen из настроек в yaml файле.
     *
     * @param string $pathToYaml
     *
     * @return \marvin255\bxcodegen\Bxcodegen
     *
     * @throws \InvalidArgumentException
     */
    protected static function createCodegenFromYaml($pathToYaml)
    {
        $realPathToYaml = realpath($pathToYaml);
        if (!file_exists($realPathToYaml)) {
            throw new InvalidArgumentException(
                "Can't find yaml with settings: {$pathToYaml}"
            );
        }

        $rootFolder = pathinfo($realPathToYaml, PATHINFO_DIRNAME);
        $defaultOptions = [
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
        $yamlOptions = (new SymfonyYaml)->parseFromFile($realPathToYaml);
        $options = new Collection(array_merge_recursive($defaultOptions, $yamlOptions));
        $locator = new ServiceLocator;

        return new Bxcodegen($options, $locator);
    }
}
