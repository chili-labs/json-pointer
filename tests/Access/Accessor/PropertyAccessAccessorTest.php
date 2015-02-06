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

    public function supportDataProvider()
    {
        return array(
            array(false, array()),
            array(true, new \ArrayObject()),
            array(false, ''),
            array(false, 123),
            array(false, 1.2),
            array(true, new \stdClass()),
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

    public function accessorDataProvider()
    {
        return array(
            array(null, new TestObject(), '', false, false),
            array(null, new TestObject(), 'not', false, false),
            array('private', new TestObject(), 'privateProperty', true, true),
            array('protected', new TestObject(), 'protectedProperty', true, true),
            array('public', new TestObject(), 'publicProperty', true, true),
            array('readable', new TestObject(), 'privateReadableProperty', true, false),
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
    public function testWritable($unused, $node, $path, $unused2, $expected)
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

    public function writeDataProvider()
    {
        return array(
            array(new TestObject(), 'not', 1, false),
            array(new TestObject(), 'privateProperty', 1, true),
            array(new TestObject(), 'protectedProperty', 1, true),
            array(new TestObject(), 'publicProperty', 1, true),
            array(new TestObject(), 'privateReadableProperty', 1, false),
        );
    }

    /**
     * @dataProvider writeDataProvider
     *
     * @param array  $document
     * @param string $path
     * @param mixed  $value
     * @param bool   $success
     */
    public function testSet($document, $path, $value, $success)
    {
        if ($success) {
            $this->accessor->set($document, $path, $value);
            $this->assertEquals($this->accessor->get($document, $path), $value);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->set($document, $path, $value);
        }
    }

    /**
     * @dataProvider writeDataProvider
     *
     * @param array  $document
     * @param string $path
     * @param mixed  $value
     * @param bool   $success
     */
    public function testCreate($document, $path, $value, $success)
    {
        if ($success) {
            $this->accessor->create($document, $path, $value);
            $this->assertEquals($this->accessor->get($document, $path), $value);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->create($document, $path, $value);
        }
    }

    /**
     * @dataProvider writeDataProvider
     *
     * @param array  $document
     * @param string $path
     * @param mixed  $value
     * @param bool   $success
     */
    public function testDelete($document, $path, $value, $success)
    {
        if ($success) {
            $this->accessor->delete($document, $path);
            $this->assertEquals($this->accessor->get($document, $path), null);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->accessor->delete($document, $path);
        }
    }
}
