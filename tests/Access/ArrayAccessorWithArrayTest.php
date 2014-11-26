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
class ArrayAccessorWithArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    protected function setUp()
    {
        $this->accessor = new ArrayAccessor();
    }

    public function accessorDataProvider()
    {
        return array(
            array(array('abc' => null), array('abc' => null), '', true),
            array(array(), array(), '', true),
            array(null, array(), '/not', false),
            array(1, array('' => 1), '/', true),
            array(null, array('abc' => 1), '/', false),
            array(1, array('abc' => 1), '/abc', true),
            array(null, array('abd' => 1), '/abc', false),
            array(1234, array('abc' => array('' => 1234)), '/abc/', true),
            array(null, array('abc' => array('def' => 1234)), '/abc/', false),
            array(1234, array('abc' => array('def' => 1234)), '/abc/def', true),
            array(null, array('def' => 123), '/def/', false),
        );
    }

    /**
     * @dataProvider accessorDataProvider
     *
     * @param mixed $unused
     * @param array $document
     * @param string $path
     * @param bool $expected
     */
    public function testHas($unused, $document, $path, $expected)
    {
        if ($expected) {
            $this->assertTrue(
                $this->accessor->has($document, new JsonPointer($path)),
                'Accessor must report true for this combination of document and path'
            );
        } else {
            $this->assertFalse(
                $this->accessor->has($document, new JsonPointer($path)),
                'Accessor must report false for this combination of document and path'
            );
        }
    }

    /**
     * @dataProvider accessorDataProvider
     *
     * @param mixed $expected
     * @param array $document
     * @param string $path
     * @param bool $success
     */
    public function testGet($expected, $document, $path, $success)
    {
        if ($success) {
            $this->assertEquals($expected, $this->accessor->get($document, new JsonPointer($path)));
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->get($document, new JsonPointer($path));
        }
    }

    public function setDataProvider()
    {
        return array(
            array(array(), array('123' => 123), '', array(), true),
            array(null, array('123' => 123), '/', array(), false),
            array(array('' => array()), array('' => 123), '/', array(), true),
            array(array('def' => 456), array('def' => 123), '/def', 456, true),
            array(null, array('def' => 123), '/def/', 456, false),
        );
    }

    /**
     * @dataProvider setDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     * @param mixed  $value
     * @param bool   $success
     */
    public function testSet($expected, $document, $path, $value, $success)
    {
        if ($success) {
            $result = $this->accessor->set($document, new JsonPointer($path), $value);
            $this->assertEquals($expected, $result);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->set($document, new JsonPointer($path), $value);
        }
    }
}
