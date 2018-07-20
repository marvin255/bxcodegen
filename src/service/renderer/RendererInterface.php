<?php

namespace marvin255\bxcodegen\service\renderer;

/**
 * Интерфейс для объекта, который обрабатывает шаблоны файлов перед созданием
 * единицы кода.
 */
interface RendererInterface
{
    /**
     * Применяет данные из параметра опций к шаблону и возвращает новую строку.
     *
     * @param string $pathToTemplateFile Путь к файлу шаблона
     * @param array  $options            Массив настроек
     *
     * @return string
     */
    public function renderTemplate($pathToTemplateFile, array $options = []);

    /**
     * Применяет данные из параметра опций к шаблону и записывает строку в файл.
     *
     * @param string $pathToTemplateFile Путь к файлу шаблона
     * @param string $pathToDestFile     Путь к файлу, в который будет записан полученная строка
     * @param array  $options            Массив настроек
     */
    public function renderTemplateToFile($pathToTemplateFile, $pathToDestFile, array $options = []);
}
