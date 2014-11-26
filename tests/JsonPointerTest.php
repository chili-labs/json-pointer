<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Test;

use ChiliLabs\JsonPointer\JsonPointer;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class JsonPointerTest extends \PHPUnit_Framework_TestCase
{

    public function pointerDataProvider()
    {
        return array(
            array('', array(), true),
            array('/', array(''), true),
            array('/def', array('def'), true),
            array('/def/', array('def', ''), true),
            array('def/', null, false),
            array('///def', array('', '', 'def'), true),
        );
    }

    /**
     * @dataProvider pointerDataProvider
     *
     * @param string $path
     * @param array  $expected
     * @param bool   $success
     */
    public function testToArray($path, $expected, $success)
    {
        if ($success) {
            $pointer = new JsonPointer($path);
            $this->assertEquals($expected, $pointer->toArray());
        } else {
            $this->setExpectedException('\InvalidArgumentException');
            new JsonPointer($path);
        }
    }

    /**
     * @dataProvider pointerDataProvider
     *
     * @param string $expected
     * @param array  $input
     * @param bool   $success
     */
    public function testFromArray($expected, $input, $success)
    {
        if (!$success) {
            $input = array();
            $expected = '';
        }
        $pointer = JsonPointer::fromArray($input);
        $this->assertEquals($expected, (string)$pointer);
        $this->assertEquals($input, $pointer->toArray());
    }

    /**
     * @dataProvider pointerDataProvider
     *
     * @param string $path
     * @param array  $unused
     * @param bool   $success
     */
    public function testToString($path, $unused, $success)
    {
        if ($success) {
            $pointer = new JsonPointer($path);
            $this->assertEquals($path, (string)$pointer);
        } else {
            $this->setExpectedException('\InvalidArgumentException');
            new JsonPointer($path);
        }
    }
}
