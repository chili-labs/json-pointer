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
    private $accessorList;

    /**
     * @param AccessorInterface[] $accessorList
     */
    public function __construct(array $accessorList = array())
    {
        $this->accessorList = $accessorList;
    }

    /**
     * @param AccessorInterface $accessor
     */
    public function registerAccessor(AccessorInterface $accessor)
    {
        $this->accessorList[] = $accessor;
        array_unique($this->accessorList);
    }

    /**
     * @param mixed $node
     *
     * @return AccessorInterface
     *
     * @throws NoMatchingAccessorException
     */
    public function findAccessorForNode($node)
    {
        foreach ($this->accessorList as $accessor) {
            if ($accessor->supports($node)) {
                return $accessor;
            }
        }

        throw new NoMatchingAccessorException(
            sprintf('Could not find a matching Accessor for node of type %s.', gettype($node))
        );
    }
}
