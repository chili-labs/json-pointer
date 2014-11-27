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
     * Sets the value of an already existing object/array node
     *
     * If the node does not exist an InvalidPathException will be thrown
     *
     * @param mixed       $document
     * @param JsonPointer $path
     * @param mixed       $value
     *
     * @throws InvalidPathException
     */
    public function set($document, JsonPointer $path, $value);

    /**
     * Adds a new object/array node with the specified value
     *
     * If $recursive is set to false and one of the parent nodes does not
     * exist an InvalidPathException will be thrown. The last node will
     * be created if it does not exist
     *
     * If $recursive is set to true all non existent nodes will be created
     * on the supplied path.
     *
     * $accessor->add({}, new JsonPointer('/does/not/exist'), 1)
     * This will result in an Exception
     *
     * $accessor->add({does:{not:{}}}, new JsonPointer('/does/not/exist'), 1)
     * This works and the document looks like {does:{not:{exist:1}}}
     *
     * $accessor->add({}, new JsonPointer('/does/not/exist'), 1, true)
     * This works as well and the document looks like {does:{not:{exist:1}}}
     *
     * @param mixed       $document
     * @param JsonPointer $path
     * @param mixed       $value
     * @param bool        $recursive
     *
     * @throws InvalidPathException
     */
    public function add($document, JsonPointer $path, $value, $recursive = false);

    /**
     * Removes a object/array node
     *
     * If the node to remove does not exist, no Exception will be thrown
     * To have a strict behaviour use together with has()
     *
     * @param mixed       $document
     * @param JsonPointer $path
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
