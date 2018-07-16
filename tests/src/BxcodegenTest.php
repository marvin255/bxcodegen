<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\Bxcodegen;
use marvin255\bxcodegen\ServiceLocatorInterface;
use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\options\Collection;
use marvin255\bxcodegen\generator\GeneratorInterface;
use InvalidArgumentException;

class BxcodegenTest extends BaseCase
{
    /**
     * @test
     */
    public function testRun()
    {
        $generatorName = 'generator_name_' . mt_rand();
        $optionToReturn = 'option_to_return_' . mt_rand();
        $optionFromConstruct = 'option_to_return_' . mt_rand();
        $serviceOption = 'service_option_' . mt_rand();
        $arOptions = [
            'generators' => [
                $generatorName => [
                    MockGenerator::class,
                    'option_from_construct' => $optionFromConstruct,
                    'option_to_return' => 'option_to_return_value',
                ],
            ],
            'services' => [
                [
                    MockService::class,
                    $serviceOption,
                ],
            ],
        ];

        $options = new Collection($arOptions);
        $optionsForRun = new Collection([
            'option_to_return' => $optionToReturn,
        ]);
        $locator = new ServiceLocator;

        $bxcodegen = new Bxcodegen($options, $locator);
        list($resReturn, $resCostruct) = $bxcodegen->run($generatorName, $optionsForRun);

        $this->assertSame($optionToReturn, $resReturn);
        $this->assertSame($optionFromConstruct, $resCostruct);
        $this->assertSame($serviceOption, $locator->resolve(MockService::class)->getOption());
    }

    /**
     * @test
     */
    public function testRunNoGeneratorException()
    {
        $generatorName = 'generator_name_' . mt_rand();
        $options = new Collection([]);
        $optionsForRun = new Collection([]);
        $locator = new ServiceLocator;

        $bxcodegen = new Bxcodegen($options, $locator);

        $this->setExpectedException(InvalidArgumentException::class, $generatorName);
        $bxcodegen->run($generatorName, $optionsForRun);
    }
}

/**
 * Мок для класса генератора.
 */
class MockGenerator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        return [
            $options->get('option_to_return'),
            $options->get('option_from_construct'),
        ];
    }
}

/**
 * Мок для сервиса локатора.
 */
class MockService
{
    /**
     * @var string
     */
    protected $option;

    /**
     * @var string
     */
    public function __construct($option)
    {
        $this->option = $option;
    }

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }
}
