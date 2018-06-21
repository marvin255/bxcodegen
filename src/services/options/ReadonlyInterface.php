<?php

namespace marvin255\bxcodegen\services\options;

/**
 * Интерфейс для объекта, который хранит набор данных с настройками
 * и позволяет только читать данные, без возможности изменния.
 */
interface ReadOnlyInterface
{
    /**
     * Возвращает параметр настройки по имени.
     *
     * @param string $name    Название параметра настройки
     * @param mixed  $default Значение по умолчанию, которое вернется, если параметр с таким именем не задан
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Возвращает весь массив настроек, которые хранятся в объекте.
     *
     * @return array Массив вида "название настройки => значение"
     */
    public function getAll();
}
