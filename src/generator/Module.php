<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\FileInterface;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор для создания модулей битрикса.
 */
class Module implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $templateData = $this->collectDataFromInputForTemplate($options);
        $sourcePath = $options->get('source', dirname(dirname(__DIR__)) . '/templates/module');
        $destinationPath = $locator->get('pathManager')->getAbsolutePath(
            "@modules/{$templateData['full_module_name']}"
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
                'Module name option must be a non empty string'
            );
        } elseif (!preg_match('/^([a-z0-9_]{3,})\.([a-z0-9_]{3,})$/', $name, $matches)) {
            throw new InvalidArgumentException(
                "Module name option must be in format vendor.module, got: {$name}"
            );
        }

        $return = [
            'full_module_name' => $name,
            'module_partner_name' => $matches[1],
            'module_name' => $matches[2],
            'php_namespace' => '\\' . implode('\\', array_map('ucfirst', explode('.', $name))),
            'install_class' => str_replace('.', '_', $name),
            'version_number' => $options->get('version_number', '0.0.1'),
            'version_date' => $options->get('version_date', date('Y-m-d')),
            'readable_title' => $options->get('title', ucfirst($matches[2])),
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
                if ($from instanceof FileInterface && $from->getExtension() === 'phptpl') {
                    $return = true;
                    $locator->get('renderer')->renderTemplateToFile(
                        $from->getPathname(),
                        $to->getPath() . '/' . $to->getFilename() . '.php',
                        $templateData
                    );
                }

                return $return;
            });
    }
}
