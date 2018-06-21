<?php

namespace marvin255\bxcodegen\tests;

use marvin255\bxcodegen\services\options\ReadAndWrtite;
use marvin255\bxcodegen\Exception;

class ReadAndWrtiteTest extends BaseCase
{
    public function testSet()
    {
        $paramName = 'param_name_1_' . mt_rand();
        $paramValue = 'param_value_1_' . mt_rand();

        $options = new ReadAndWrtite([]);

        $this->assertSame($options, $options->set($paramName, $paramValue));
        $this->assertSame($paramValue, $options->get($paramName));
    }

    public function testSetEmptyNameException()
    {
        $options = new ReadAndWrtite([]);

        $this->setExpectedException(Exception::class);
        $options->set(false, 123);
    }

    public function testSetAll()
    {
        $paramName = 'param_name_1_' . mt_rand();
        $paramValue = 'param_value_1_' . mt_rand();
        $paramName2 = 'param_name_2_' . mt_rand();
        $paramValue2 = 'param_value_2_' . mt_rand();

        $options = new ReadAndWrtite([]);
        $options->set($paramName, $paramValue);
        $options->setAll([$paramName2 => $paramValue2]);

        $this->assertSame(null, $options->get($paramName));
        $this->assertSame($paramValue2, $options->get($paramName2));
    }
}
