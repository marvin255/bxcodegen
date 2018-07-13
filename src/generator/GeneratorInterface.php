<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;

/**
 * Интерфейс для объекта, который собственно создает некую единицу кода, исходя
 * из настроек.
 */
interface GeneratorInterface
{
    /**
     * Создает единицу кода, на основании настроек из параметра.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $collection
     *
     * @return bool
     */
    public function generate(CollectionInterface $collection);
}
