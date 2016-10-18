<?php

namespace Devim\Provider\StorageServiceProvider\Exception;

/**
 * Class FileNotFoundException.
 */
class FileNotFoundException extends StorageException
{
    /**
     * FileNotFoundException constructor.
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(sprintf('File id "%s" not found', $fileName));
    }
}
