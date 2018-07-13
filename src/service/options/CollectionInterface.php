<?php

namespace marvin255\bxcodegen\service\options;

/**
 * Интерфейс для объекта, который хранит набор данных с настройками
 * и позволяет только читать данные, без возможности изменния.
 */
interface CollectionInterface
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

    /**
     * Сливает две коллекции настроек и возвращает новый объект настроек.
     * Опции из сливаемого объекта при совпадении имен перепишут опции текущего.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $collection
     *
     * @return \marvin255\bxcodegen\service\options\CollectionInterface
     */
    public function merge(CollectionInterface $collection);
}
