<?php

namespace marvin255\bxcodegen\services\options;

use marvin255\bxcodegen\Exception;

/**
 * Объект, который хранит набор данных с настройками
 * и позволяет только читать и записывать данные.
 */
class ReadAndWrtite extends Readonly implements ReadAndWrtiteInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxcodegen\Exception
     */
    public function set($name, $value)
    {
        if (!is_string($name) || trim($name) === '') {
            throw new Exception('Option name must be a non empty string');
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAll(array $options)
    {
        $this->options = [];

        foreach ($options as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }
}
