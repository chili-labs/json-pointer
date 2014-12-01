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
            array(null, array('abc' => null), '', false),
            array(null, array(), '/not', false),
            array(1, array('' => 1), '', true),
            array(1, array('abc' => 1), 'abc', true),
            array(null, array('abc' => null), 'abc', true),
            array(null, array('abc' => null), 'abd', false),
        );
    }

    /**
     * @dataProvider accessorDataProvider
     *
     * @param mixed  $unused
     * @param array  $node
     * @param string $path
     * @param bool   $expected
     */
    public function testReadable($unused, $node, $path, $expected)
    {
        if ($expected) {
            $this->assertTrue(
                $this->accessor->isReadable($node, $path),
                'Accessor must report true for this combination of document and path'
            );
        } else {
            $this->assertFalse(
                $this->accessor->isReadable($node, $path),
                'Accessor must report false for this combination of document and path'
            );
        }
    }

    /**
     * @dataProvider accessorDataProvider
     *
     * @param mixed  $unused
     * @param array  $node
     * @param string $path
     * @param bool   $expected
     */
    public function testWritable($unused, $node, $path, $expected)
    {
        if ($expected) {
            $this->assertTrue(
                $this->accessor->isWritable($node, $path),
                'Accessor must report true for this combination of document and path'
            );
        } else {
            $this->assertFalse(
                $this->accessor->isWritable($node, $path),
                'Accessor must report false for this combination of document and path'
            );
        }
    }

    /**
     * @dataProvider accessorDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     * @param bool   $success
     */
    public function testGet($expected, $document, $path, $success)
    {
        if ($success) {
            $this->assertEquals($expected, $this->accessor->get($document, $path));
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->get($document, $path);
        }
    }

    public function setDataProvider()
    {
        return array(
            array(null, array('123' => 123), '', array(), false),
            array(null, array('123' => 123), 'abc', array(), false),
            array(array('123' => 1), array('123' => 123), '123', 1, true),
            array(array('' => array()), array('' => 123), '', array(), true),
            array(array(0, 1, 5), array(0, 1), '2', 5, false),
            array(array(0, 5), array(0, 1), '1', 5, true),
            array(array('a' => 0, 1 => 5), array('a' => 0, 1 => 1), '1', 5, true),
            array(array(0, 5), array(0, 1), '-', 5, false),
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
            $this->accessor->set($document, $path, $value);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->set($document, $path, $value);
        }
    }

    public function createDataProvider()
    {
        return array(
            array(array('' => 1, '123' => 123), array('123' => 123), '', 1, true),
            array(array('' => array()), array('' => 123), '', array(), false),
            array(array('def' => 456), array(), 'def', 456, true),
            array(array(0, 1, 5), array(0, 1), '2', 5, true),
            array(array(0, 5, 1), array(0, 1), '1', 5, false),
            array(null, array('a' => 0, 1 => 1), '1', 5, false),
            array(array(0, 1, '-' => 5), array(0, 1), '-', 5, true),
        );
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     * @param mixed  $value
     * @param bool   $success
     */
    public function testCreate($expected, $document, $path, $value, $success)
    {
        if ($success) {
            $this->accessor->create($document, $path, $value);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->create($document, $path, $value);
        }
    }

    public function deleteDataProvider()
    {
        return array(
            array(null, array('123' => 123), 'abs', false),
            array(array(), array('123' => 123), '123', true),
        );
    }

    /**
     * @dataProvider deleteDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     * @param bool   $success
     */
    public function testDelete($expected, $document, $path, $success)
    {
        if ($success) {
            $this->accessor->delete($document, $path);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->delete($document, $path);
        }
    }
}
