<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\services\options\Collection;

class CollectionTest extends BaseCase
{
    /**
     * @test
     */
    public function testGet()
    {
        $paramName = 'param_name_1_' . mt_rand();
        $paramValue = 'param_value_1_' . mt_rand();
        $paramName2 = 'param_name_2_' . mt_rand();
        $paramValue2 = 'param_value_2_' . mt_rand();
        $paramNameUnexisted = 'param_name_unexisted_' . mt_rand();
        $paramValueDefault = 'param_name_unexisted_' . mt_rand();

        $options = new Collection([
            $paramName => $paramValue,
            $paramName2 => $paramValue2,
        ]);

        $this->assertSame($paramValue, $options->get($paramName));
        $this->assertSame($paramValue2, $options->get($paramName2));
        $this->assertSame(null, $options->get($paramNameUnexisted));
        $this->assertSame($paramValueDefault, $options->get($paramNameUnexisted, $paramValueDefault));
    }

    /**
     * @test
     */
    public function testGetAll()
    {
        $params = [
            'param_name_1_' . mt_rand() => 'param_value_1_' . mt_rand(),
            'param_name_2_' . mt_rand() => 'param_value_2_' . mt_rand(),
        ];

        $options = new Collection($params);

        $this->assertSame($params, $options->getAll());
    }
}
