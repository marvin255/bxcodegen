<?php

namespace marvin255\bxcodegen\tests\service\filesystem;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\service\filesystem\PathHelper;

class PathHelperTest extends BaseCase
{
    /**
     * @test
     */
    public function testUnify()
    {
        $pathes = [
            [
                'test' => ' \/\111\222/333\\/ ',
                'separator' => '/',
                'expected' => '/111/222/333',
            ],
            [
                'test' => ' \/\111\222/333\\/ ',
                'separator' => '\\',
                'expected' => '\\111\\222\\333',
            ],
            [
                'test' => ' C:\\111/222 333\\ ',
                'separator' => '\\',
                'expected' => 'C:\\111\\222 333',
            ],
        ];

        foreach ($pathes as $test) {
            $this->assertSame(
                $test['expected'],
                PathHelper::unify($test['test'], $test['separator'])
            );
        }
    }

    /**
     * @test
     */
    public function testCombine()
    {
        $pathes = [
            [
                'test' => ['', '111', '222', '333'],
                'separator' => '/',
                'expected' => '/111/222/333',
            ],
            [
                'test' => ['111', '222', '333'],
                'separator' => '\\',
                'expected' => '111\\222\\333',
            ],
            [
                'test' => ['C:', '111', '222 333'],
                'separator' => '\\',
                'expected' => 'C:\\111\\222 333',
            ],
        ];

        foreach ($pathes as $test) {
            $this->assertSame(
                $test['expected'],
                PathHelper::combine($test['test'], $test['separator'])
            );
        }
    }

    /**
     * @test
     */
    public function testIsAbsolute()
    {
        $pathes = [
            [
                'test' => ' \/\111\222/333\\/ ',
                'separator' => '/',
                'expected' => true,
            ],
            [
                'test' => ' 111\222/333\\/ ',
                'separator' => '/',
                'expected' => false,
            ],
            [
                'test' => ' \/\111\222/333\\/ ',
                'separator' => '\\',
                'expected' => true,
            ],
            [
                'test' => ' 111\222/333\\/ ',
                'separator' => '\\',
                'expected' => false,
            ],
            [
                'test' => ' C:\\111/222 333\\ ',
                'separator' => '\\',
                'expected' => true,
            ],
            [
                'test' => ' c:\\111/222 333\\ ',
                'separator' => '\\',
                'expected' => true,
            ],
            [
                'test' => ' test\\111/222 333\\ ',
                'separator' => '\\',
                'expected' => false,
            ],
            [
                'test' => ' test:\\111/222 333\\ ',
                'separator' => '\\',
                'expected' => false,
            ],
        ];

        foreach ($pathes as $test) {
            $res = PathHelper::isAbsolute($test['test'], $test['separator']);
            if ($test['expected']) {
                $this->assertTrue($res, $test['test']);
            } else {
                $this->assertFalse($res, $test['test']);
            }
        }
    }
}
