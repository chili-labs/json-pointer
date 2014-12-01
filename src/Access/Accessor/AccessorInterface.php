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

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
interface AccessorInterface
{
    /**
     * @param mixed $node
     *
     * @return bool
     */
    public function supports($node);

    /**
     * @param mixed  $node
     * @param string $singlePath
     *
     * @return mixed
     *
     * @throws InvalidPathException
     */
    public function &get(&$node, $singlePath);

    /**
     * Sets the value of an already existing object/array node
     *
     * If the node does not exist an InvalidPathException will be thrown
     *
     * @param mixed  $node
     * @param string $singlePath
     * @param mixed  $value
     *
     * @throws InvalidPathException
     */
    public function set(&$node, $singlePath, $value);

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
     * @param mixed  $node
     * @param string $singlePath
     * @param mixed  $value
     *
     * @throws InvalidPathException
     */
    public function create(&$node, $singlePath, $value);

    /**
     * Removes a object/array node
     *
     * If the node to remove does not exist, no Exception will be thrown
     * To have a strict behaviour use together with has()
     *
     * @param mixed  $node
     * @param string $singlePath
     */
    public function delete(&$node, $singlePath);

    /**
     * @param mixed  $node
     * @param string $singlePath
     *
     * @return bool
     */
    public function isReadable($node, $singlePath);

    /**
     * @param mixed  $node
     * @param string $singlePath
     *
     * @return bool
     */
    public function isWritable($node, $singlePath);
}
