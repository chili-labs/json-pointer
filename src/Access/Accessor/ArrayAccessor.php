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
class ArrayAccessor implements AccessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($document)
    {
        return is_array($document) || $document instanceof \ArrayAccess && $document instanceof \Countable;
    }

    /**
     * {@inheritdoc}
     */
    public function get($document, JsonPointer $path)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            return $this->doGet($document, $pathElements);
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function set($document, JsonPointer $path, $value)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->doSetOrRemove($document, $pathElements, $value);
        } else {
            $document = $value;
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($document, JsonPointer $path)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->doSetOrRemove($document, $pathElements);
        } else {
            $document = null;
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function has($document, JsonPointer $path)
    {
        try {
            $this->get($document, $path);
        } catch (InvalidPathException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param array $node
     * @param array $path
     *
     * @return mixed
     *
     * @throws InvalidPathException
     */
    private function doGet($node, array $path)
    {
        $key = array_shift($path);
        if (!array_key_exists($key, $node)) {
            throw new InvalidPathException(sprintf('The element "%s" does not exist.', $key));
        }

        if (count($path) === 0) {
            return $node['-' === $key ? count($node) - 1 : $key];
        }

        if (!$this->supports($node[$key])) {
            throw new InvalidPathException(sprintf('The element "%s" is invalid.', $key));
        }

        return $this->doGet($node[$key], $path);
    }

    /**
     * When called without 3rd parameter (not setting it to null!) the path
     * will be unset in the document
     *
     * @param array      $node
     * @param array      $path
     * @param mixed|null $value
     *
     * @throws InvalidPathException
     */
    private function doSetOrRemove(&$node, array $path, $value = null)
    {
        $key = array_shift($path);

        if (count($path) === 0) {
            $key = '-' === $key ? count($node) : $key;

            if (2 === func_num_args()) {
                // If it was called without 3rd parameter we are removing
                unset($node[$key]);
            } elseif (is_numeric($key)) {
                // Insert at desired index in the array or ArrayAccess
                $nodeCount = count($node);
                for ($i = (int) $key; $i < $nodeCount; $i++) {
                    $oldValue = $node[$i];
                    $node[$i] = $value;
                    $value = $oldValue;
                }
                $node[$nodeCount] = $value;
            } else {
                $node[$key] = $value;
            }

            return;
        }

        if (!array_key_exists($key, $node) || !is_array($node[$key])) {
            throw new InvalidPathException(
                sprintf('The element "%s" does not exist or is invalid.', $key)
            );
        }

        $this->doSetOrRemove($node[$key], $path, $value);
    }
}
