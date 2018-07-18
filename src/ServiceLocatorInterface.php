<?php

namespace marvin255\bxcodegen;

/**
 * Интерфейс для объекта, который позволяет передавать объекты сервисов
 * между задачами, например, объекты pdo для связи с базой данных.
 */
interface ServiceLocatorInterface
{
    /**
     * Пробует найти сервис по названию, если такой зарегистрирован.
     *
     * @param string $alias
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function get($alias);

    /**
     * Регистрирует сервис по указаным названием.
     *
     * @param string $alias
     * @param mixed  $service
     *
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function set($alias, $service);
}
