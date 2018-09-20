<?php

namespace marvin255\bxcodegen;

use marvin255\bxcodegen\service\options\Collection;
use marvin255\bxcodegen\service\yaml\SymfonyYaml;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\generator\Component;
use marvin255\bxcodegen\generator\Module;
use marvin255\bxcodegen\generator\Rocketeer;
use marvin255\bxcodegen\cli\GeneratorCommand;
use marvin255\bxcodegen\cli\ComponentCommand;
use marvin255\bxcodegen\cli\ModuleCommand;
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
        $app->add((new ComponentCommand)->setBxcodegen($bxcodegen));
        $app->add((new ModuleCommand)->setBxcodegen($bxcodegen));

        return $app;
    }

    /**
     * Создает менеджер генераторов с дефолтными настройками.
     *
     * @param string $workDir
     *
     * @return \marvin255\bxcodegen\Bxcodegen
     */
    public static function createDefault($workDir)
    {
        $arOptions = [
            'services' => [
                'pathManager' => [
                    PathManager::class,
                    $workDir,
                    [
                        'components' => '/web/local/components',
                        'modules' => '/web/local/modules',
                    ],
                ],
                'renderer' => [
                    Twig::class,
                ],
                'copier' => [
                    Copier::class,
                ],
            ],
            'generators' => [
                'rocketeer' => [
                    'class' => Rocketeer::class,
                ],
                'component' => [
                    'class' => Component::class,
                ],
                'module' => [
                    'class' => Module::class,
                ],
            ],
        ];

        return new Bxcodegen(new Collection($arOptions), new ServiceLocator);
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
    protected static function createCodegenFromYaml($pathToYaml = null)
    {
        $realPathToYaml = $pathToYaml ? realpath($pathToYaml) : false;

        if ($realPathToYaml && file_exists($realPathToYaml)) {
            $arOptions = self::getOptionsFromYaml($realPathToYaml);
        } else {
            throw new InvalidArgumentException("Yaml file doesn't exist");
        }

        return new Bxcodegen(new Collection($arOptions), new ServiceLocator);
    }

    /**
     * Получает список настроек из yaml файла.
     *
     * @param string $pathToYaml
     *
     * @return array
     */
    protected static function getOptionsFromYaml($pathToYaml)
    {
        $raw = (new SymfonyYaml)->parseFromFile($pathToYaml) ?: [];

        return self::setReplacesToArray($raw, [
            '@currFile' => $pathToYaml,
            '@currDir' => pathinfo($pathToYaml, PATHINFO_DIRNAME),
        ]);
    }

    /**
     * Производит замену плейсхолдеров на предопределенные значения.
     *
     * @param array $params
     * @param array $replaces
     *
     * @return array
     */
    protected static function setReplacesToArray(array $params, array $replaces)
    {
        $return = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $return[$key] = self::setReplacesToArray($value, $replaces);
            } elseif (array_key_exists($value, $replaces)) {
                $return[$key] = $replaces[$value];
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}
