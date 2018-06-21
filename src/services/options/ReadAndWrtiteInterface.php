<?php

namespace marvin255\bxcodegen\services\options;

/**
 * Интерфейс для объекта, который хранит набор данных с настройками
 * и позволяет читать и записывать данные.
 */
interface ReadAndWrtiteInterface extends ReadOnlyInterface
{
    /**
     * Задает параметр состояния по имени.
     *
     * @param string $name  Название параметра настройки
     * @param mixed  $value Значение параметра настройки
     *
     * @return self
     */
    public function set($name, $value);

    /**
     * Задает весь массив настроек, очищая старые настройки.
     *
     * @param array $options
     *
     * @return self
     */
    public function setAll(array $options);
}
