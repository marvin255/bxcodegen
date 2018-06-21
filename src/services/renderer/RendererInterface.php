<?php

namespace marvin255\bxcodegen\services\renderer;

use marvin255\bxcodegen\services\options\CollectionInterface;

/**
 * Интерфейс для объекта, который обрабатывает шаблоны файлов перед созданием
 * единицы кода.
 */
interface RendererInterface
{
    /**
     * Задает параметр состояния по имени.
     *
     * @param string              $template Идентификатор шаблона
     * @param CollectionInterface $options  Массив настроек
     *
     * @return string
     */
    public function render($template, CollectionInterface $options);
}
