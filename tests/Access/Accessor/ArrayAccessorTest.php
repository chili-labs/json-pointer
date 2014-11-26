<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Test\Access\Accessor;

use ChiliLabs\JsonPointer\Access\Accessor\AccessorInterface;
use ChiliLabs\JsonPointer\Access\Accessor\ArrayAccessor;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class ArrayAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    protected function setUp()
    {
        $this->accessor = new ArrayAccessor();
    }

    public function supportDataProvider()
    {
        return array(
            array(true, array()),
            array(true, new \ArrayObject()),
            array(false, ''),
            array(false, 123),
            array(false, 1.2),
            array(false, new \stdClass()),
            array(false, true),
            array(false, false),
            array(false, null),
        );
    }

    /**
     * @dataProvider supportDataProvider
     *
     * @param bool  $expected
     * @param mixed $document
     */
    public function testSupports($expected, $document)
    {
        if ($expected) {
            $this->assertTrue(
                $this->accessor->supports($document),
                'Accessor does not support document of type '.gettype($document)
            );
        } else {
            $this->assertFalse(
                $this->accessor->supports($document),
                'Accessor must not support document of type '.gettype($document)
            );
        }
    }
}
