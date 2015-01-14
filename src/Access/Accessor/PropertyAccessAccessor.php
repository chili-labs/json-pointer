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
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class PropertyAccessAccessor implements AccessorInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccess;

    public function __construct(PropertyAccessor $propertyAccess)
    {
        $this->propertyAccess = $propertyAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($node)
    {
        return is_object($node);
    }

    /**
     * {@inheritdoc}
     */
    public function &get(&$node, $singlePath)
    {
        if (!$singlePath || !$this->isReadable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s is not readable', $singlePath));
        }

        $value = $this->propertyAccess->getValue($node, $singlePath);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set(&$node, $singlePath, $value)
    {
        if (!$this->isWritable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s is not writable', $singlePath));
        }

        $this->propertyAccess->setValue($node, $singlePath, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function create(&$node, $singlePath, $value)
    {
        if (!$this->isWritable($node, $singlePath)) {
            throw new InvalidPathException(
                sprintf('Path %s could not be created in object of type %s', $singlePath, gettype($node))
            );
        }

        $this->propertyAccess->setValue($node, $singlePath, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(&$node, $singlePath)
    {
        $this->set($node, $singlePath, null);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($node, $singlePath)
    {
        if (!$singlePath) {
            return false;
        }

        return $this->propertyAccess->isReadable($node, $singlePath);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($node, $singlePath)
    {
        if (!$singlePath) {
            return false;
        }

        return $this->propertyAccess->isWritable($node, $singlePath);
    }
}
