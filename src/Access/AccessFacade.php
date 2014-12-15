<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer\Access;

use ChiliLabs\JsonPointer\Exception\InvalidPathException;
use ChiliLabs\JsonPointer\Exception\NoMatchingAccessorException;
use ChiliLabs\JsonPointer\JsonPointer;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class AccessFacade
{
    /**
     * @var AccessorFactory
     */
    private $factory;

    /**
     * @param AccessorFactory $factory
     */
    public function __construct(AccessorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @return mixed
     *
     * @throws InvalidPathException
     */
    public function get($node, JsonPointer $pointer)
    {
        $value = $node;
        foreach ($pointer->toArray() as $singlePath) {
            $accessor = $this->getAccessorForNode($value, $pointer, $singlePath);
            $value = $accessor->get($value, $singlePath);
        }

        return $value;
    }

    /**
     * Sets the value of an already existing object/array node
     *
     * If the node does not exist an InvalidPathException will be thrown
     *
     * @param mixed       $node
     * @param JsonPointer $pointer
     * @param mixed       $value
     *
     * @throws InvalidPathException
     */
    public function set(&$node, JsonPointer $pointer, $value)
    {
        $pathElements = $pointer->toArray();
        if (!$pathElements) {
            $node = $value;

            return;
        }

        $lastPath = array_pop($pathElements);
        $lastNode = & $this->getNodeReference($node, JsonPointer::fromArray($pathElements));
        $accessor = $this->getAccessorForNode($lastNode, $pointer, $lastPath);

        if (!$accessor->isWritable($lastNode, $lastPath)) {
            throw new InvalidPathException(sprintf('The path %s in %s is not writable', $lastPath, $pointer));
        }

        $accessor->set($lastNode, $lastPath, $value);
    }

    /**
     * Adds a new object/array node with the specified value.
     *
     * $accessor->add({}, new JsonPointer('/does/not/exist'), 1)
     * This will result in an Exception
     *
     * $accessor->add({does:{not:{}}}, new JsonPointer('/does/not/exist'), 1)
     * This works and the document looks like {does:{not:{exist:1}}}
     *
     * @param mixed       $node
     * @param JsonPointer $pointer
     * @param mixed       $value
     *
     * @throws InvalidPathException
     */
    public function create(&$node, JsonPointer $pointer, $value)
    {
        $pathElements = $pointer->toArray();
        if (!$pathElements) {
            $node = $value;

            return;
        }
        $lastPath = array_pop($pathElements);
        $lastNode = & $this->getNodeReference($node, JsonPointer::fromArray($pathElements));
        $lastPath = $this->checkAndTransformKey($lastPath, $lastNode);

        if (is_int($lastPath)) {
            // We now know that the node is an array and we want to insert the value
            $this->insertIntoArray($lastNode, $lastPath, $value);
        } else {
            $accessor = $this->getAccessorForNode($lastNode, $pointer, $lastPath);
            $accessor->create($lastNode, $lastPath, $value);
        }
    }

    /**
     * Removes a object/array node
     *
     * If the last node to remove does not exist, no Exception will be thrown
     * To have a strict behaviour use together with has()
     *
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @throws InvalidPathException
     */
    public function delete(&$node, JsonPointer $pointer)
    {
        $pathElements = $pointer->toArray();
        if (!$pathElements) {
            throw new InvalidPathException('Cannot delete root document');
        }
        $lastPath = array_pop($pathElements);
        $lastNode = & $this->getNodeReference($node, JsonPointer::fromArray($pathElements));

        $accessor = $this->getAccessorForNode($lastNode, $pointer, $lastPath);
        $isAssocArray = $this->isAssociativeArray($lastNode);
        $accessor->delete($lastNode, $lastPath);

        if (!$isAssocArray) {
            $lastNode = array_values($lastNode);
        }
    }

    /**
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @return bool
     */
    public function isReadable($node, JsonPointer $pointer)
    {
        return $this->checkAccess('Readable', $node, $pointer);
    }

    /**
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @return bool
     */
    public function isWritable($node, JsonPointer $pointer)
    {
        return $this->checkAccess('Writable', $node, $pointer);
    }

    /**
     * @param string      $mode
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @return bool
     */
    private function checkAccess($mode, $node, JsonPointer $pointer)
    {
        $pathElements = $pointer->toArray();
        $lastPath = array_pop($pathElements);
        try {
            $lastNode = &$this->getNodeReference($node, JsonPointer::fromArray($pathElements));
        } catch (InvalidPathException $exception) {
            return false;
        }
        $accessor = $this->getAccessorForNode($lastNode, $pointer, $lastPath);

        return $accessor->{'is'.$mode}($lastNode, $lastPath);
    }

    /**
     * @param mixed       $node
     * @param JsonPointer $pointer
     *
     * @return mixed
     */
    private function &getNodeReference(&$node, JsonPointer $pointer)
    {
        foreach ($pointer->toArray() as $singlePath) {
            $accessor = $this->getAccessorForNode($node, $pointer, $singlePath);
            $node = & $accessor->get($node, $singlePath);
        }

        return $node;
    }

    /**
     * @param mixed       $node
     * @param JsonPointer $pointer
     * @param string      $currentPath
     *
     * @return Accessor\AccessorInterface
     */
    private function getAccessorForNode($node, JsonPointer $pointer, $currentPath)
    {
        try {
            $accessor = $this->factory->findAccessorForNode($node);
        } catch (NoMatchingAccessorException $exception) {
            throw new InvalidPathException(
                sprintf(
                    'The path %s in %s contains an not supported type: %s',
                    $currentPath,
                    $pointer,
                    gettype($node)
                ),
                0,
                $exception
            );
        }

        return $accessor;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isAssociativeArray(array $array)
    {
        for (reset($array); is_int(key($array)); next($array)) {
        }

        return !is_null(key($array));
    }

    /**
     * @param string $key
     * @param array  $node
     *
     * @return int|string
     *
     * @throws InvalidPathException
     */
    private function checkAndTransformKey($key, $node)
    {
        if ('-' === $key || $this->isIntegerKey($key)) {
            if (!is_array($node) || $this->isAssociativeArray($node)) {
                throw new InvalidPathException(
                    sprintf('Used "%s" but array was not an array or is associative.', $key)
                );
            }
            $key = '-' === $key ? count($node) : (int) $key;
        }

        return $key;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    private function isIntegerKey($key) {
        return is_numeric($key) && ((string) (int) $key) === $key;
    }

    /**
     * @param array  $node
     * @param string $index
     * @param mixed  $value
     */
    private function insertIntoArray(array &$node, $index, $value)
    {
        if ($index < count($node)) {
            array_splice($node, $index, 0, array($value));
        } else {
            $node[$index] = $value;
        }
        $node = array_values($node);
    }
}
