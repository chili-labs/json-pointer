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

use ChiliLabs\JsonPointer\Access\Accessor\AccessorInterface;
use ChiliLabs\JsonPointer\Exception\NoMatchingAccessorException;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
class AccessorFactory
{
    /**
     * @var AccessorInterface[]
     */
    private $accessors;

    /**
     * @param AccessorInterface[] $accessors
     */
    public function __construct(array $accessors)
    {
        $this->accessors = $accessors;
    }

    /**
     * @param mixed $document
     *
     * @return AccessorInterface
     *
     * @throws NoMatchingAccessorException
     */
    public function findAccessorForDocument($document)
    {
        foreach ($this->accessors as $accessor) {
            if ($accessor->supports($document)) {
                return $accessor;
            }
        }

        throw new NoMatchingAccessorException(
            sprintf('Could not find a matching Accessor for document of type %s.', gettype($document))
        );
    }
}
