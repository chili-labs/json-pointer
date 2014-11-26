<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Test\Access;

use ChiliLabs\JsonPointer\Access\AccessorInterface;
use ChiliLabs\JsonPointer\Access\ArrayAccessor;
use ChiliLabs\JsonPointer\JsonPointer;

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

    public function testSupports()
    {
        $this->assertTrue($this->accessor->supports(array()));
        $this->assertTrue($this->accessor->supports(new \ArrayObject()));
        $this->assertFalse($this->accessor->supports(''));
        $this->assertFalse($this->accessor->supports(123));
        $this->assertFalse($this->accessor->supports(1.2));
        $this->assertFalse($this->accessor->supports(new \stdClass()));
        $this->assertFalse($this->accessor->supports(false));
        $this->assertFalse($this->accessor->supports(true));
        $this->assertFalse($this->accessor->supports(null));
    }

    public function testHasWithArray()
    {
        $this->assertTrue($this->accessor->has(array('abc' => null), new JsonPointer('')));
        $this->assertTrue($this->accessor->has(array(), new JsonPointer('')));
        $this->assertTrue($this->accessor->has(array('' => 1), new JsonPointer('/')));
        $this->assertFalse($this->accessor->has(array('abc' => 1), new JsonPointer('/')));
        $this->assertTrue($this->accessor->has(array('abc' => 1), new JsonPointer('/abc')));
        $this->assertFalse($this->accessor->has(array('abd' => 1), new JsonPointer('/abc')));
        $this->assertTrue($this->accessor->has(array('abc' => array('' => 1234)), new JsonPointer('/abc/')));
        $this->assertFalse($this->accessor->has(array('abc' => array('def' => 1234)), new JsonPointer('/abc/')));
        $this->assertTrue($this->accessor->has(array('abc' => array('def' => 1234)), new JsonPointer('/abc/def')));
    }

    public function testGetWithArray()
    {
        $this->assertEquals(array('abc' => null), $this->accessor->get(array('abc' => null), new JsonPointer('')));
        $this->assertEquals(array(), $this->accessor->get(array(), new JsonPointer('')));
        $this->assertEquals(1, $this->accessor->get(array('' => 1), new JsonPointer('/')));
        $this->assertEquals(1, $this->accessor->get(array('abc' => 1), new JsonPointer('/abc')));
        $this->assertEquals(1234, $this->accessor->get(array('abc' => array('' => 1234)), new JsonPointer('/abc/')));
        $this->assertEquals(1234, $this->accessor->get(array('a' => array('d' => 1234)), new JsonPointer('/a/d')));
    }

    /**
     * @expectedException \ChiliLabs\JsonPointer\Exception\InvalidPathException
     */
    public function testGetWithArrayAndInvalidPath()
    {
        $this->accessor->get(array('abc' => 1), new JsonPointer('/'));

    }
}
