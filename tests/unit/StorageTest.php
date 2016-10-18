<?php

use Devim\Provider\StorageServiceProvider\StorageServiceProvider;
use Devim\Provider\StorageServiceProvider\Storage\FileStorage;
use Devim\Provider\StorageServiceProvider\Exception\FileNotFoundException;
use Silex\Application;

/**
 * Class StorageTest
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileStorage
     */
    protected $storageService;
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new StorageServiceProvider());
        $this->app['storage.directory'] = __DIR__.'/_fixtures/storage';
        $this->app['storage.public_url_template'] = '/static/{assetId}';

        $this->storageService = $this->app['storage'];
    }

    public function testStorageServiceProvider()
    {
        self::assertArrayHasKey('storage', $this->app);
        self::assertInstanceOf(FileStorage::class, $this->app['storage']);
    }

    public function testPutString()
    {
        self::assertInternalType('string', $this->storageService->put('test123'));
        self::assertNotEmpty($this->storageService->put('test123'));
    }

    public function testGetUrl()
    {
        self::assertInternalType('string', $this->storageService->getUrl('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
        self::assertNotEmpty($this->storageService->getUrl('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
    }

    public function testGetRaw()
    {
        self::assertInternalType('string', $this->storageService->getRaw('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
        self::assertNotEmpty($this->storageService->getRaw('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
    }

    public function testGetRawException()
    {
        $this->expectException(FileNotFoundException::class);
        $this->storageService->getRaw('fail');
    }

    public function testGetMimeType()
    {
        self::assertInternalType('string', $this->storageService->getMimeType('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
        self::assertNotEmpty($this->storageService->getMimeType('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
    }

    public function testGetMimeTypeException()
    {
        $this->expectException(FileNotFoundException::class);
        $this->storageService->getMimeType('fail');
    }

    public function testExists()
    {
        self::assertTrue($this->storageService->exists('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
    }

    public function testExistsFalse()
    {

        self::assertFalse($this->storageService->exists('fail'));
    }

    public function testRemove()
    {
        self::assertTrue($this->storageService->remove('7288edd0fc3ffcbe93a0cf06e3568e28521687bc.txt'));
    }

    public function testRemoveException()
    {
        $this->expectException(FileNotFoundException::class);
        $this->storageService->remove('fail');
    }
}
