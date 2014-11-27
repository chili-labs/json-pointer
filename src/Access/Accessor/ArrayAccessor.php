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
        return is_array($document);
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
    public function set(&$document, JsonPointer $path, $value)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->doSet($document, $pathElements, $value);
        } else {
            $document = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(&$document, JsonPointer $path, $value, $recursive = false)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->doAdd($document, $pathElements, $value, $recursive);
        } else {
            $document = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(&$document, JsonPointer $path)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->doRemove($document, $pathElements);
        } else {
            $document = null;
        }
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
     * @param array      $node
     * @param array      $path
     * @param mixed|null $value
     * @param bool       $recursive
     *
     * @throws InvalidPathException
     */
    private function doAdd(array &$node, array $path, $value, $recursive)
    {
        $key = $this->checkAndTransformKey(array_shift($path), $node);

        if (count($path) === 0) {
            if (array_key_exists($key, $node) && !is_int($key)) {
                throw new InvalidPathException(sprintf('The node "%s" does already exist.', $key));
            }
            $this->doAddOrSetValue($node, $value, $key);

            return;
        }

        if (!array_key_exists($key, $node)) {
            if ($recursive) {
                $node[$key] = array();
            } else {
                throw new InvalidPathException(
                    sprintf('The node "%s" does not exist or is invalid.', $key)
                );
            }
        }

        if (!$this->supports($node[$key])) {
            throw new InvalidPathException(sprintf('The node "%s" is invalid.', $key));
        }

        $this->doAdd($node[$key], $path, $value, $recursive);
    }

    /**
     * @param array      $node
     * @param array      $path
     * @param mixed|null $value
     *
     * @throws InvalidPathException
     */
    private function doSet(array &$node, array $path, $value)
    {
        $key = $this->checkAndTransformKey(array_shift($path), $node, false, false);

        if (!array_key_exists($key, $node)) {
            throw new InvalidPathException(
                sprintf('The element "%s" does not exist.', $key)
            );
        }

        if (count($path) === 0) {
            $node[$key] = $value;

            return;
        }

        if (!$this->supports($node[$key])) {
            throw new InvalidPathException(
                sprintf('The element "%s" is not valid.', $key)
            );
        }

        $this->doSet($node[$key], $path, $value);
    }

    /**
     * @param array $node
     * @param array $path
     *
     * @throws InvalidPathException
     */
    private function doRemove(array &$node, array $path)
    {
        $key = $this->checkAndTransformKey(array_shift($path), $node, false, false);

        if (count($path) === 0) {
            unset($node[$key]);
            if (is_int($key)) {
                $node = array_values($node);
            }

            return;
        }

        if (!array_key_exists($key, $node) || !$this->supports($node[$key])) {
            throw new InvalidPathException(
                sprintf('The element "%s" does not exist or is invalid.', $key)
            );
        }

        $this->doRemove($node[$key], $path);
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
     * @param bool   $numericNeedsArray
     * @param bool   $allowDash
     *
     * @return int|string
     *
     * @throws InvalidPathException
     */
    private function checkAndTransformKey($key, array $node, $numericNeedsArray = true, $allowDash = true)
    {
        if ('-' === $key) {
            if (!$allowDash) {
                throw new InvalidPathException(sprintf('Used "-" but not allowed in this context.', $key));
            }
            if ($this->isAssociativeArray($node)) {
                throw new InvalidPathException('Used "-" but array was associative.');
            }
            $key = count($node);
        }

        if (is_numeric($key) && ((string) (int) $key) === $key) {
            if ($numericNeedsArray && $this->isAssociativeArray($node)) {
                throw new InvalidPathException('Used numeric value but array was associative.');
            }
            $key = (int) $key;
        }

        return $key;
    }

    /**
     * @param array  $node
     * @param mixed  $value
     * @param string $key
     */
    private function doAddOrSetValue(array &$node, $value, $key)
    {
        if (is_int($key)) {
            if ($key < count($node)) {
                array_splice($node, $key, 0, array($value));
            } else {
                $node[$key] = $value;
            }
            $node = array_values($node);
        } else {
            $node[$key] = $value;
        }
    }
}
