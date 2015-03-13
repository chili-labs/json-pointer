<?php

/*
 * This file is part of the json-pointer library.
 *
 * (c) Daniel Tschinder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChiliLabs\JsonPointer;

/**
 * @author Daniel Tschinder <daniel@tschinder.de>
 */
interface JsonPointerInterface
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function __toString();
}
