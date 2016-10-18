<?php

namespace Devim\Provider\StorageServiceProvider\Storage;

/**
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * @param mixed $resource
     * @param null $ext
     *
     * @return string
     */
    public function put($resource, $ext = null) : string;

    /**
     * @param string $id
     *
     * @return string
     */
    public function getUrl(string $id) : string;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function remove(string $id) : bool;

    /**
     * @param string $id
     * @param bool $asResource
     *
     * @return mixed
     */
    public function getRaw(string $id, bool $asResource = false);

    /**
     * @param string $id
     *
     * @return string
     */
    public function getMimeType(string $id) : string;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function exists(string $id) : bool;
}
