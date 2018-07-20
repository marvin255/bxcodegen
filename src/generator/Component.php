<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\FileInterface;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор для создания компонентов битрикса.
 */
class Component implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $templateData = $this->collectDataFromInputForTemplate($options);
        $sourcePath = $options->get('source', dirname(dirname(__DIR__)) . '/templates/component');
        $destinationPath = $locator->get('pathManager')->getAbsolutePath(
            "@components/{$templateData['component_namespace']}/{$templateData['component_name']}"
        );

        $copier = $this->getAndConfigurateCopierFromLocator($locator, $templateData);
        $source = new Directory($sourcePath);
        $destination = new Directory($destinationPath);

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
        $name = $options->get('name');

        if (!$name) {
            throw new InvalidArgumentException(
                'Name option must be a non empty string'
            );
        } elseif (!preg_match('/^([a-zA-Z0-9_]{3,}):([a-zA-Z0-9_\.]{3,})$/', $name, $nameParts)) {
            throw new InvalidArgumentException(
                "Name option must be in format namespace:name, got: {$name}"
            );
        }

        $nameParts = array_map('strtolower', $nameParts);

        $return = [
            'full_component_name' => $name,
            'php_namespace' => $nameParts[1],
            'php_class' => ucfirst(str_replace('.', '', $nameParts[2])),
            'component_namespace' => $nameParts[1],
            'component_name' => $nameParts[2],
            'readable_title' => $options->get(
                'title',
                str_replace('.', ' ', ucfirst($nameParts[2]))
            ),
        ];

        return $return;
    }

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
        return $locator->get('copier')
            ->clearTransformers()
            ->addTransformer(function ($from, $to) use ($locator, $templateData) {
                $return = false;
                if ($from instanceof FileInterface && $from->getExtension() === 'phptwig') {
                    $return = true;
                    $fileContent = $locator->get('renderer')->renderFile(
                        $from->getPathname(),
                        $templateData
                    );
                    file_put_contents(
                        $to->getPath() . '/' . $to->getFilename() . '.php',
                        $fileContent
                    );
                }

                return $return;
            });
    }
}
