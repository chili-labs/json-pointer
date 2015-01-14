<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Test\Fixtures;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class TestObject
{
    public $publicProperty = 'public';

    private $protectedProperty = 'protected';

    private $privateProperty = 'private';

    private $privateReadablePRoperty = 'readable';

    private $array = array();

    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    public function setProtectedProperty($protectedProperty)
    {
        $this->protectedProperty = $protectedProperty;
    }

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function setPrivateProperty($privateProperty)
    {
        $this->privateProperty = $privateProperty;
    }

    public function getPrivateReadableProperty()
    {
        return $this->privateReadablePRoperty;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function setArray($array)
    {
        $this->array = $array;
    }
}
