<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\filesystem\FileInterface;
use marvin255\bxcodegen\ServiceLocatorInterface;

/**
 * Абстрактный класс для генераторов.
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * Получает объект-копировщик из service locator и настраивает его.
     *
     * @param \marvin255\bxcodegen\ServiceLocatorInterface $locator
     * @param array                                        $templateData
     *
     * @return \marvin255\bxcodegen\service\filesystem\CopierInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getAndConfigurateCopierFromLocator(ServiceLocatorInterface $locator, array $templateData)
    {
        //отображение шаблона
        $renderer = function ($from, $to) use ($locator, $templateData) {
            $return = false;
            if ($from instanceof FileInterface && $from->getExtension() === 'phptpl') {
                $return = true;
                $locator->get('renderer')->renderTemplateToFile(
                    $from->getPathname(),
                    $to->getPath() . '/' . $to->getFilename() . '.php',
                    $templateData
                );
            }

            return $return;
        };

        return $locator->get('copier')->clearTransformers()->addTransformer($renderer);
    }
}
