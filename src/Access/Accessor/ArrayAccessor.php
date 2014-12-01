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
class ArrayAccessor implements AccessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($node)
    {
        return is_array($node);
    }

    /**
     * {@inheritdoc}
     */
    public function &get(&$node, $singlePath)
    {
        if (!$this->isReadable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s does not exist', $singlePath));
        }

        return $node[$singlePath];
    }

    /**
     * {@inheritdoc}
     */
    public function set(&$node, $singlePath, $value)
    {
        if (!$this->isWritable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s does not exist', $singlePath));
        }

        $node[$singlePath] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function create(&$node, $singlePath, $value)
    {
        if ($this->isReadable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s can not be created, already exists', $singlePath));
        }
        $node[$singlePath] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(&$node, $singlePath)
    {
        if (!$this->isReadable($node, $singlePath)) {
            throw new InvalidPathException(sprintf('Path %s can not be deleted, does not exist', $singlePath));
        }
        unset($node[$singlePath]);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($node, $singlePath)
    {
        return array_key_exists($singlePath, $node);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($node, $singlePath)
    {
        return $this->isReadable($node, $singlePath);
    }
}
