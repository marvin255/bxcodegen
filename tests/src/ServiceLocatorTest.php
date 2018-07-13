<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\ServiceLocatorInterface;

class ServiceLocatorTest extends BaseCase
{
    /**
     * @test
     */
    public function testResolve()
    {
        $service = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceClass = get_class($service);

        $locator = new ServiceLocator;
        $locator->register($service);

        $this->assertSame($service, $locator->resolve($serviceClass));
    }
}
