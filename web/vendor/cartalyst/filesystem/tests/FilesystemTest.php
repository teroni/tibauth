<?php

/**
 * Part of the Filesystem package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Filesystem
 * @version    3.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Filesystem\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Cartalyst\Filesystem\Filesystem;

class FilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the filesystem manager instance.
     *
     * @var \Cartalyst\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');

        $filesystem = new Filesystem($adapter);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');

        $filesystem = new Filesystem($adapter);

        $this->assertInstanceOf('Cartalyst\Filesystem\Filesystem', $filesystem);
    }

    /** @test */
    public function it_can_set_overwrite_flag()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');

        $filesystem = new Filesystem($adapter);

        $filesystem->overwrite(true);
    }

    /** @test */
    public function it_can_put()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')
            ->times(3);

        $adapter->shouldReceive('write')
            ->once();

        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $manager->shouldReceive('getPlaceholders')
            ->twice()
            ->andReturn([]);

        $manager->shouldReceive('getDispersion')
            ->twice();

        $filesystem = m::mock('Cartalyst\Filesystem\Filesystem[getManager]', [$adapter]);
        $filesystem->shouldReceive('getManager')
            ->twice()
            ->andReturn($manager);

        $filesystem->put('foo.txt', 'foobar');
    }

    /** @test */
    public function it_can_update()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')
            ->once()
            ->andReturn(true);

        $adapter->shouldReceive('update')
            ->once()
            ->andReturn(false);

        $filesystem = m::mock('Cartalyst\Filesystem\Filesystem[getManager]', [$adapter]);

        $filesystem->update('foo.txt', 'foobar');
    }

    /** @test */
    public function it_can_upload()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')
            ->times(3);

        $adapter->shouldReceive('write')
            ->once();

        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $manager->shouldReceive('getPlaceholders')
            ->twice()
            ->andReturn([]);

        $manager->shouldReceive('getDispersion')
            ->twice();

        $filesystem = m::mock('Cartalyst\Filesystem\Filesystem[getManager]', [$adapter]);
        $filesystem->shouldReceive('getManager')
            ->twice()
            ->andReturn($manager);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getExtension')
            ->once()
            ->andReturn('txt');

        $file->shouldReceive('getMimeType')
            ->once()
            ->andReturn('image/png');

        $file->shouldReceive('getPathname')
            ->once()
            ->andReturn(__DIR__.'/files/test.png');

        $filesystem->upload($file, 'test1.png');
    }

    /**
     * @test
     * @expectedException \Cartalyst\Filesystem\Exceptions\FileExistsException
     */
    public function it_throws_an_exception_if_file_exists_and_overwrite_is_false()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')
            ->times(3)
            ->andReturn(true);

        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $manager->shouldReceive('getPlaceholders')
            ->twice()
            ->andReturn([]);

        $manager->shouldReceive('getDispersion')
            ->twice();

        $filesystem = m::mock('Cartalyst\Filesystem\Filesystem[getManager]', [$adapter]);
        $filesystem->overwrite(false);
        $filesystem->shouldReceive('getManager')
            ->twice()
            ->andReturn($manager);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getExtension')
            ->once()
            ->andReturn('txt');

        $file->shouldReceive('getMimeType')
            ->once()
            ->andReturn('image/png');

        $file->shouldReceive('getPathname')
            ->once()
            ->andReturn(__DIR__.'/files/test.png');

        $filesystem->upload($file, 'test1.txt');
    }

    /** @test */
    public function it_set_the_destination()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');

        $filesystem = new Filesystem($adapter);

        $filesystem->saveTo('foo');
    }

    /** @test */
    public function it_can_validate_files()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $file    = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $filesystem = new Filesystem($adapter);

        $file->shouldReceive('getSize')->once();

        $manager->shouldReceive('getMaxFileSize')->once();
        $manager->shouldReceive('getAllowedMimes')->once();

        $filesystem->setManager($manager);
        $filesystem->validateFile($file);
    }

    /**
     * @test
     * @expectedException \Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException
     */
    public function it_throws_an_exception_if_file_is_larger_than_max()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $file    = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $filesystem = new Filesystem($adapter);

        $file->shouldReceive('getSize')->once()->andReturn(20);

        $manager->shouldReceive('getMaxFileSize')->once();
        $manager->shouldReceive('getAllowedMimes')->once();

        $filesystem->setManager($manager);
        $filesystem->validateFile($file);
    }

    /**
     * @test
     * @expectedException \Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException
     */
    public function it_throws_an_exception_if_file_has_invalid_mime_type()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $file    = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $filesystem = new Filesystem($adapter);

        $file->shouldReceive('getSize')->once();
        $file->shouldReceive('getClientMimeType')->once();

        $manager->shouldReceive('getMaxFileSize')->once();
        $manager->shouldReceive('getAllowedMimes')->once()->andReturn(['image/jpeg']);

        $filesystem->setManager($manager);
        $filesystem->validateFile($file);
    }

    /**
     * @test
     */
    public function it_can_prepare_the_file_location_from_string()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');

        $filesystem = new Filesystem($adapter);

        $filesystem->setManager($manager);

        $manager->shouldReceive('getPlaceholders')->once()->andReturn([':yyyy' => '2014', ':mm' => '01']);
        $manager->shouldReceive('getDispersion')->once()->andReturn(':yyyy/:mm/');

        $this->assertEquals('2014/01/file.png', $filesystem->prepareFileLocation('file.png'));
    }

    /**
     * @test
     */
    public function it_can_prepare_the_file_location_from_uploaded_file()
    {
        $adapter = m::mock('League\Flysystem\AdapterInterface');
        $manager = m::mock('Cartalyst\Filesystem\FilesystemManager');
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $filesystem = new Filesystem($adapter);

        $filesystem->setManager($manager);

        $manager->shouldReceive('getPlaceholders')->once()->andReturn([':yyyy' => '2014', ':mm' => '01']);
        $manager->shouldReceive('getDispersion')->once()->andReturn(':extension/:yyyy/:mm/');

        $file->shouldReceive('getExtension')->once()->andReturn('jpg');
        $file->shouldReceive('getMimeType')->once()->andReturn('image/jpeg');

        $this->assertEquals('jpg/2014/01/file.png', $filesystem->prepareFileLocation($file, 'file.png'));
    }
}
