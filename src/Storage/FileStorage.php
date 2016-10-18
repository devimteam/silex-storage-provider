<?php

namespace Devim\Provider\StorageServiceProvider\Storage;

use Devim\Provider\StorageServiceProvider\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorage implements StorageInterface
{
    /**
     * @var
     */
    private $storageDirectory;

    /**
     * @var
     */
    private $publicUrlTemplate;

    /**
     * FileStorage constructor.
     *
     * @param $storageDirectory
     * @param $publicUrlTemplate
     */
    public function __construct($storageDirectory, $publicUrlTemplate)
    {
        $this->storageDirectory = $storageDirectory;
        $this->publicUrlTemplate = $publicUrlTemplate;
    }

    /**
     * @param mixed $resource
     * @param null $ext
     *
     * @return string
     */
    public function put($resource, $ext = null) : string
    {
        $tmpFilename = tempnam('/tmp', 'tmp_');

        if ($resource instanceof UploadedFile) {
            $pathInfo = pathinfo($tmpFilename);
            $resource->move($pathInfo['dirname'], $pathInfo['basename']);
        } else {
            file_put_contents($tmpFilename, $resource);
        }

        $checkSum = sha1_file($tmpFilename);
        $file = new File($tmpFilename);
        $targetFilename = $checkSum . '.' . ($ext === null ? $file->guessExtension() : $ext);
        $target = $file->move($this->storageDirectory, $targetFilename);

        chmod($target, 0777);

        return $targetFilename;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getUrl(string $id) : string
    {
        return strtr($this->publicUrlTemplate, ['{assetId}' => $id]);
    }

    /**
     * @param string $id
     *
     * @return bool
     *
     * @throws FileNotFoundException
     */
    public function remove(string $id) : bool
    {
        if (!$this->exists($id)) {
            throw new FileNotFoundException($id);
        }

        return unlink($this->getSourceFileName($id));
    }

    /**
     * @param string $id
     * @param bool $asResource
     *
     * @return mixed
     *
     * @throws FileNotFoundException
     */
    public function getRaw(string $id, bool $asResource = false)
    {
        if (!$this->exists($id)) {
            throw new FileNotFoundException($id);
        }

        $source = $this->getSourceFileName($id);

        if ($asResource) {
            return fopen($source, 'rb');
        }

        return file_get_contents($source);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getSourceFileName(string $id) : string
    {
        return $this->storageDirectory . '/' . $id;
    }

    /**
     * @param string $id
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function getMimeType(string $id) : string
    {
        if (!$this->exists($id)) {
            throw new FileNotFoundException($id);
        }

        return (new File($this->getSourceFileName($id)))->getMimeType();
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function exists(string $id) : bool
    {
        $source = $this->getSourceFileName($id);

        return is_file($source) && file_exists($source);
    }
}
