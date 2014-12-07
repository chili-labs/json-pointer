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

use ChiliLabs\JsonPointer\Access\AccessFacade;
use ChiliLabs\JsonPointer\Access\Accessor\ArrayAccessor;
use ChiliLabs\JsonPointer\Access\Accessor\PropertyAccessAccessor;
use ChiliLabs\JsonPointer\Access\AccessorFactory;
use ChiliLabs\JsonPointer\JsonPointer;
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
        $factory = new AccessorFactory(array(
            new ArrayAccessor(),
            new PropertyAccessAccessor(PropertyAccess::createPropertyAccessor())
        ));
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
        return array(
            array(array(), array('123' => 123), '', array(), true),
            array(array('' => 1, '123' => 123), array('123' => 123), '/', 1, false),
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
            array(array(0, 1, 5), array(0, 1), '/2', 5, false),
            array(array(0, 5), array(0, 1), '/1', 5, true),
            array(array('a' => 0, 1 => 5), array('a' => 0, 1 => 1), '/1', 5, true),
            array(array(0, 5), array(0, 1), '/-', 5, false),
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
}