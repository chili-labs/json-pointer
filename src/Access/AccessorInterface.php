<?php
namespace ChiliLabs\JsonPointer\Access;

use ChiliLabs\JsonPointer\JsonPointer;

interface AccessorInterface
{
    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function supports($document);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     *
     * @return mixed
     */
    public function get($document, JsonPointer $path);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     * @param mixed       $value
     *
     * @return
     */
    public function set($document, JsonPointer $path, $value);

    /**
     * @param mixed       $document
     * @param JsonPointer $path
     *
     * @return bool
     */
    public function has($document, JsonPointer $path);
}
