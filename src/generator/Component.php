<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\ServiceLocatorInterface;

/**
 * Генератор для создания компонентов битрикса.
 */
class Component implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
    }
}
