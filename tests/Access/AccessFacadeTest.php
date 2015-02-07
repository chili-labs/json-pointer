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

use ChiliLabs\JsonPointer\Access\AccessFacade;
use ChiliLabs\JsonPointer\Access\Accessor\ArrayAccessor;
use ChiliLabs\JsonPointer\Access\Accessor\PropertyAccessAccessor;
use ChiliLabs\JsonPointer\Access\AccessorFactory;
use ChiliLabs\JsonPointer\JsonPointer;
use ChiliLabs\JsonPointer\Test\Fixtures\TestObject;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class AccessFacadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessFacade
     */
    private $facade;

    protected function setUp()
    {
        $factory = new AccessorFactory(array(new ArrayAccessor()));
        $factory->registerAccessor(new PropertyAccessAccessor(PropertyAccess::createPropertyAccessor()));
        $this->facade = new AccessFacade($factory);
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
     * @param mixed  $expected
     * @param array  $node
     * @param string $path
     * @param bool   $success
     */
    public function testGet($expected, $node, $path, $success)
    {
        if ($success) {
            $this->assertEquals(
                $expected,
                $this->facade->get($node, new JsonPointer($path)),
                'Accessor must report true for this combination of document and path'
            );
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->facade->get($node, new JsonPointer($path));
        }
    }

    public function setDataProvider()
    {
        $array = new TestObject();
        $array->setArray(array(array(new TestObject())));

        $expected = new TestObject();
        $tmp = new TestObject();
        $tmp->setPrivateProperty(5);
        $expected->setArray(array(array($tmp)));

        return array(
            array(array(), array('123' => 123), '', array(), true),
            array(null, array('123' => 123), '/', 1, false),
            array(array('' => array()), array('' => 123), '/', array(), true),
            array(array('def' => 456), array('def' => 123), '/def', 456, true),
            array(
                array('abc' => array('def' => array('ghi' => 321))),
                array('abc' => array('def' => array('ghi' => 123))),
                '/abc/def/ghi',
                321,
                true,
            ),
            array(null, array('def' => 123), '/def/', 456, false),
            array(null, array('q' => array('bar' => 2)), '/a/b', 456, false),
            array(null, array(0, 1), '/2', 5, false),
            array(array(0, 5), array(0, 1), '/1', 5, true),
            array(array('a' => 0, 1 => 5), array('a' => 0, 1 => 1), '/1', 5, true),
            array(null, array(0, 1), '/-', 5, false),
            array($expected, $array, '/array/0/0/privateProperty', 5, true),
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
            $this->facade->set($document, new JsonPointer($path), $value);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->facade->set($document, new JsonPointer($path), $value);
        }
    }

    public function createDataProvider()
    {
        return array(
            array(array(), array('123' => 123), '', array(), true),
            array(array('123' => 456), array(), '/123', 456, true),
            array(array('' => 1, '123' => 123), array('123' => 123), '/', 1, true),
            array(array('' => array()), array('' => 123), '/', array(), false),
            array(array('def' => 456), array('def' => 123), '/def', 456, false),
            array(
                array('abc' => array('def' => array('ghi' => 321))),
                array('abc' => array('def' => array('ghi' => 123))),
                '/abc/def/ghi',
                321,
                false,
            ),
            array(null, array('def' => 123), '/def/', 456, false),
            array(null, array('q' => array('bar' => 2)), '/a/b', 456, false),
            array(array(0, 1, 5), array(0, 1), '/2', 5, true),
            array(array(0, 5, 1), array(0, 1), '/1', 5, true),
            array(array('a' => 0, 1 => 5), array('a' => 0, 1 => 1), '/1', 5, false),
            array(array(0, 1, 5), array(0, 1), '/-', 5, true),
            array(array(1, 5), array('0' => 1), '/-', 5, true),
            array(array(5), array(), '/-', 5, true),
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
            $this->facade->create($document, new JsonPointer($path), $value);
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->facade->create($document, new JsonPointer($path), $value);
        }
    }

    public function deleteDataProvider()
    {
        return array(
            array(null, array('123' => 123), '', false),
            array(array('123' => 123), array('' => 1, '123' => 123), '/', true),
            array(null, array('123' => 123), '/', false),
            array(array(), array('def' => 123), '/def', true),
            array(
                array('abc' => array('def' => array())),
                array('abc' => array('def' => array('ghi' => 123))),
                '/abc/def/ghi',
                true,
            ),
            array(array(0, 1), array(0, 1, 5), '/2', true),
            array(array(0, 5), array(0, 1, 5), '/1', true),
            array(array('a' => 0), array('a' => 0, 1 => 1), '/1', true),
            array(null, array(0, 1), '/-', false),
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
            $this->facade->delete($document, new JsonPointer($path));
            $this->assertEquals($expected, $document);
        } else {
            $this->setExpectedException('\ChiliLabs\JsonPointer\Exception\InvalidPathException');
            $this->facade->delete($document, new JsonPointer($path));
        }
    }

    public function isReadableDataProvider()
    {
        // TODO test with objects
        return array(
            array(false, array('123' => 123), ''),
            array(false, array('123' => 123), '/'),
            array(true, array('123' => 123), '/123'),
            array(false, array('def' => 123), '/def/123'),
            array(true, array('def' => array('abc' => 123)), '/def/abc'),
            array(false, array('def' => array('abc' => 123)), '/def/abcd'),
            array(false, array(0, 1), '/2'),
            array(true, array(0, 1), '/1'),
            array(false, array(0, 1), '/-'),
        );
    }

    /**
     * @dataProvider isReadableDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     */
    public function testIsReadable($expected, $document, $path)
    {
        $result = $this->facade->isReadable($document, new JsonPointer($path));
        $this->assertEquals($expected, $result);
    }

    public function isWritableDataProvider()
    {
        // TODO test with objects
        return array(
            array(false, array('123' => 123), ''),
            array(false, array('123' => 123), '/'),
            array(true, array('123' => 123), '/123'),
            array(false, array('def' => 123), '/def/123'),
            array(true, array('def' => array('abc' => 123)), '/def/abc'),
            array(false, array('def' => array('abc' => 123)), '/def/abcd'),
            array(false, array(0, 1), '/2'),
            array(true, array(0, 1), '/1'),
            array(false, array(0, 1), '/-'),
        );
    }

    /**
     * @dataProvider isWritableDataProvider
     *
     * @param mixed  $expected
     * @param array  $document
     * @param string $path
     */
    public function testIsWritable($expected, $document, $path)
    {
        $result = $this->facade->isWritable($document, new JsonPointer($path));
        $this->assertEquals($expected, $result);
    }
}
