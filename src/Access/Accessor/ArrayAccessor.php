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
        return is_array($document) || $document instanceof \ArrayAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function get($document, JsonPointer $path)
    {
        foreach ($path->toArray() as $pathPart) {
            if (!is_array($document) || !array_key_exists($pathPart, $document)) {
                throw new InvalidPathException(
                    sprintf('The element "%s" in the path "%s" does not exist in the document.', $pathPart, $path)
                );
            }
            $document = $document[$pathPart];
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function set($document, JsonPointer $path, $value)
    {
        $element = &$document;
        foreach ($path->toArray() as $pathParts) {
            if (!is_array($element) || !array_key_exists($pathParts, $element)) {
                throw new InvalidPathException(
                    sprintf('The element "%s" in the path "%s" does not exist in the document.', $pathParts, $path)
                );
            }
            $element = &$element[$pathParts];
        }

        $element = $value;

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function has($document, JsonPointer $path)
    {
        foreach ($path->toArray() as $pathPart) {
            if (!is_array($document) || !array_key_exists($pathPart, $document)) {
                return false;
            }
            $document = $document[$pathPart];
        }

        return true;
    }
}
