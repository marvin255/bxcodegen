<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор для создания компонентов битрикса.
 */
class Component extends AbstractGenerator
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
        $name = $options->get('name');

        if (!$name) {
            throw new InvalidArgumentException(
                'Name option must be a non empty string'
            );
        }

        if (mb_strpos($name, ':') === false && $options->get('default_namespace')) {
            $name = $options->get('default_namespace') . ':' . $name;
        }

        if (!preg_match('/^([a-zA-Z0-9_\.]{3,}):([a-zA-Z0-9_\.]{3,})$/', $name, $nameParts)) {
            throw new InvalidArgumentException(
                "Name option must be in format namespace:name, got: {$name}"
            );
        }

        $nameParts = array_map('strtolower', $nameParts);
        $namespace = implode('', array_map('ucfirst', explode('.', $nameParts[1])));
        $className = implode('', array_map('ucfirst', preg_split('/(\.|_)/', $nameParts[2])));

        $return = [
            'full_component_name' => $name,
            'php_namespace' => $namespace,
            'php_class' => $className,
            'component_namespace' => $nameParts[1],
            'component_name' => $nameParts[2],
            'readable_title' => $options->get(
                'title',
                str_replace('.', ' ', ucfirst($nameParts[2]))
            ),
        ];

        return $return;
    }
}
