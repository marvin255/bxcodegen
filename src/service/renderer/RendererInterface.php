<?php

namespace marvin255\bxcodegen\service\renderer;

/**
 * Интерфейс для объекта, который обрабатывает шаблоны файлов перед созданием
 * единицы кода.
 */
interface RendererInterface
{
    /**
     * Задает параметр состояния по имени.
     *
     * @param string $pathToTemplateFile Путь к файлу шаблона
     * @param array  $options            Массив настроек
     *
     * @return string
     */
    public function renderFile($pathToTemplateFile, array $options = []);
}
