<?php

namespace marvin255\bxcodegen\services\options;

/**
 * Объект, который хранит набор данных с настройками
 * и позволяет только читать данные, без возможности изменния.
 */
class Readonly implements ReadonlyInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->options)
            ? $this->options[$name]
            : $default;
    }
}
