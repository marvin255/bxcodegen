<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

class ServiceLocatorTest extends BaseCase
{
    /**
     * @test
     */
    public function testSet()
    {
        $serviceName = 'service_name_' . mt_rand();
        $service = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();

        $locator = new ServiceLocator;

        $this->assertSame($locator, $locator->set($serviceName, $service));
        $this->assertSame($service, $locator->get($serviceName));
    }

    /**
     * @test
     */
    public function testSetWrongAliasException()
    {
        $serviceName = ' service_name_' . mt_rand();
        $service = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();

        $locator = new ServiceLocator;

        $this->setExpectedException(InvalidArgumentException::class, $serviceName);
        $locator->set($serviceName, $service);
    }

    /**
     * @test
     */
    public function testGetUnexistedServiceException()
    {
        $serviceName = ' service_name_' . mt_rand();

        $locator = new ServiceLocator;

        $this->setExpectedException(InvalidArgumentException::class, $serviceName);
        $locator->get($serviceName);
    }
}
