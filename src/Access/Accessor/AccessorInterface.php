<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Access\Accessor;

use ChiliLabs\JsonPointer\Exception\InvalidPathException;
use ChiliLabs\JsonPointer\JsonPointer;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
interface AccessorInterface
{
    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function supports($document);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     *
     * @return mixed
     *
     * @throws InvalidPathException
     */
    public function get($document, JsonPointer $path);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     * @param mixed       $value
     *
     * @return mixed
     *
     * @throws InvalidPathException
     */
    public function set($document, JsonPointer $path, $value);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     *
     * @return mixed
     */
    public function remove($document, JsonPointer $path);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     *
     * @return bool
     *
     * @throws InvalidPathException
     */
    public function has($document, JsonPointer $path);
}
