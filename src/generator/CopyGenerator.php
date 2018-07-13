<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\CopierInterface;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор, который создает единицу кода простым копированием папки с эталоном
 * в указанную папку назначения.
 */
abstract class CopyGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $this->validateOptions($options);

        $source = new Directory($options->get('source'));
        $destination = new Directory($options->get('destination'));
        $this->locator->getCopier($source, $destination);

        return true;
    }

    /**
     * Создает полный объект опций из опци самого генератора и опций для
     * конкретной операции по генерации кода.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $operationOptions
     *
     * @return \marvin255\bxcodegen\service\options\CollectionInterface
     */
    protected function createMergedOptionsArray(CollectionInterface $operationOptions)
    {
        return $this->options->merge($operationOptions);
    }

    /**
     * Валидирует массив с опциями.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $operationOptions
     *
     * @throws \InvalidArgumentException
     */
    protected function validateOptions(CollectionInterface $operationOptions)
    {
        $source = $options->get('source');
        if (empty($source)) {
            throw new InvalidArgumentException(
                "Can't find source option for copying"
            );
        } elseif (!is_dir($source)) {
            throw new InvalidArgumentException(
                "Source {$source} isn't existing dir"
            );
        }

        $destination = $options->get('destination');
        if (empty($destination)) {
            throw new InvalidArgumentException(
                "Can't find destination option for copying"
            );
        } elseif (!is_writable($destination)) {
            throw new InvalidArgumentException(
                "Destination {$source} isn't writable"
            );
        }
    }

    /**
     * Получает объект, который отвечает за копирование содержимого.
     *
     * @return \marvin255\bxcodegen\service\filesystem\CopierInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getCopier()
    {
        $copier = $this->locator->resolve(CopierInterface::class);
        if (!$copier) {
            throw new InvalidArgumentException(
                "Can't find " . CopierInterface::class . ' service'
            );
        }

        $copier->clearTransformers();

        return $copier;
    }
}
