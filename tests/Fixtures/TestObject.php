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

    private $array = array();

    /**
     * @return mixed
     */
    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    /**
     * @param mixed $protectedProperty
     */
    public function setProtectedProperty($protectedProperty)
    {
        $this->protectedProperty = $protectedProperty;
    }

    /**
     * @return mixed
     */
    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    /**
     * @param mixed $privateProperty
     */
    public function setPrivateProperty($privateProperty)
    {
        $this->privateProperty = $privateProperty;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param array $array
     */
    public function setArray($array)
    {
        $this->array = $array;
    }
}
