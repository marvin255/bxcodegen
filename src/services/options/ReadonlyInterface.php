<?php

namespace marvin255\bxcodegen\services\options;

/**
 * Интерфейс для объекта, который хранит набор данных с настройками
 * и позволяет только читать данные, без возможности изменния.
 */
interface ReadOnlyInterface
{
    /**
     * Возвращает параметр состояния по имени.
     *
     * @param string $name    Название параметра остояния
     * @param mixed  $default Название параметра остояния
     *
     * @return mixed
     */
    public function get($name, $default = null);
}
