<?php

namespace marvin255\bxcodegen\services\renderer;

use marvin255\bxcodegen\services\options\ReadOnlyInterface;

/**
 * Интерфейс для объекта, который обрабатывает шаблоны файлов перед созданием
 * единицы кода.
 */
interface RendererInterface
{
    /**
     * Задает параметр состояния по имени.
     *
     * @param string                                                  $template Имя шаблона
     * @param \marvin255\bxcodegen\services\options\ReadOnlyInterface $options  Массив настроек
     *
     * @return string
     */
    public function render($template, ReadOnlyInterface $options);
}
