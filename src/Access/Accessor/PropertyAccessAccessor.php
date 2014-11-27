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
use Symfony\Component\PropertyAccess\PropertyAccess;
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

    public function __construct()
    {
        $this->propertyAccess = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($document)
    {
        return is_object($document);
    }

    /**
     * {@inheritdoc}
     */
    public function get($document, JsonPointer $path)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            return $this->propertyAccess->getValue($document, implode('.', $pathElements));
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
            $this->propertyAccess->setValue($document, implode('.', $pathElements), $value);
        } else {
            $document = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(&$document, JsonPointer $path, $value, $recursive = false)
    {
        if (!$this->has($document, $path)) {
            throw new InvalidPathException(sprintf('The path "%s" does not exist.', $path));
        }

        $this->set($document, $path, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(&$document, JsonPointer $path)
    {
        $pathElements = $path->toArray();

        if (count($pathElements) > 0) {
            $this->propertyAccess->setValue($document, implode('.', $pathElements), null);
        } else {
            $document = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($document, JsonPointer $path)
    {
        return $this->propertyAccess->isReadable($document, implode('.', $path->toArray()));
    }
}
