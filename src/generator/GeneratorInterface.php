<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\ServiceLocatorInterface;

/**
 * Интерфейс для объекта, который собственно создает некую единицу кода, исходя
 * из настроек.
 */
interface GeneratorInterface
{
    /**
     * Создает единицу кода, на основании настроек из параметра.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $options
     * @param \marvin255\bxcodegen\ServiceLocatorInterface             $locator
     *
     * @return bool
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator);
}
