<?php

namespace marvin255\bxcodegen;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\options\Collection;
use ReflectionClass;
use InvalidArgumentException;

/**
 * Объект приложения кодогенератора.
 */
class Bxcodegen
{
    /**
     * @var \marvin255\bxcodegen\service\options\CollectionInterface
     */
    protected $options;
    /**
     * @var \marvin255\bxcodegen\ServiceLocatorInterface
     */
    protected $locator;
    /**
     * @var bool
     */
    protected $isServicesInited = false;

    /**
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $options
     * @param \marvin255\bxcodegen\ServiceLocatorInterface             $locator
     */
    public function __construct(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $this->options = $options;
        $this->locator = $locator;
    }

    /**
     * Инициирует соответствующий генератор и запускает на выполнение.
     *
     * @param string                                                   $generatorName
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $options
     *
     * @throws \InvalidArgumentException
     */
    public function run($generatorName, CollectionInterface $operationOptions)
    {
        $generators = $this->options->get('generators');
        if (empty($generators[$generatorName]) || !is_array($generators[$generatorName])) {
            throw new InvalidArgumentException(
                "Can't find {$generatorName} generator"
            );
        }

        $options = $generators[$generatorName];
        $class = array_shift($options);

        $defaultOptions = new Collection($options);
        $generator = new $class;

        $generator->generate(
            $defaultOptions->merge($operationOptions),
            $this->initLocator()
        );
    }

    /**
     * Инициирует сервисы, которые определены в настройках.
     *
     * @return \marvin255\bxcodegen\ServiceLocatorInterface
     */
    protected function initLocator()
    {
        $services = $this->options->get('services');

        if (is_array($services) && !$this->isServicesInited) {
            $this->isServicesInited = true;
            foreach ($services as $serviceDescription) {
                $class = array_shift($serviceDescription);
                $reflect = new ReflectionClass($class);
                $instance = $reflect->newInstanceArgs($serviceDescription);
                $this->locator->register($instance);
            }
        }

        return $this->locator;
    }
}
