<?php

namespace marvin255\bxcodegen;

use InvalidArgumentException;

/**
 * Объект, который позволяет передавать объекты сервисов
 * между задачами, например, объекты pdo для связи с базой данных.
 */
class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * @inheritdoc
     */
    public function get($alias)
    {
        if (empty($this->services[$alias])) {
            throw new InvalidArgumentException(
                "Can't find service {$alias}"
            );
        }

        return $this->services[$alias];
    }

    /**
     * @inheritdoc
     */
    public function set($alias, $service)
    {
        if (!preg_match('/^[a-z0-9_]{3,}$/', $alias)) {
            throw new InvalidArgumentException(
                'Alias name must consist of more than 2 symbols of latin, digits and _.'
                . " Got: {$alias}"
            );
        }

        $this->services[$alias] = $service;

        return $this;
    }
}
