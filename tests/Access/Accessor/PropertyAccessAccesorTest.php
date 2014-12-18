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
use ChiliLabs\JsonPointer\Access\Accessor\PropertyAccessAccessor;
use ChiliLabs\JsonPointer\Test\Fixtures\TestObject;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class PropertyAccessAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    protected function setUp()
    {
        $this->accessor = new PropertyAccessAccessor(PropertyAccess::createPropertyAccessor());
    }

    public function accessorDataProvider()
    {
        return array(
            array(null, new TestObject(), '', false),
            array(null, new TestObject(), 'not', false),
            array('private', new TestObject(), 'privateProperty', true),
            array('protected', new TestObject(), 'protectedProperty', true),
            array('public', new TestObject(), 'publicProperty', true),
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
        if ($success) {
            $this->accessor->delete($document, $path);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->delete($document, $path);
        }
    }
}
