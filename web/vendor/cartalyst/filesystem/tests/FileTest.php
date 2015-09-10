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
use Cartalyst\Filesystem\File;
use PHPUnit_Framework_TestCase;

class FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * File instance.
     *
     * @var \Cartalyst\Filesystem\File
     */
    protected $file;

    /**
     * Filesystem instance.
     *
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * Setup resources and dependencies
     */
    public function setUp()
    {
        $this->filesystem = $filesystem = m::mock('League\Flysystem\FilesystemInterface');

        $this->file = new File($filesystem, __DIR__.'/files/test.png');
    }

    /** @test */
    public function it_can_retrieve_the_file_contents()
    {
        $this->filesystem->shouldReceive('read')
            ->with(__DIR__.'/files/test.png')
            ->once();

        $this->file->getContents();
    }

    /** @test */
    public function it_can_retrieve_the_file_name()
    {
        $this->assertEquals('test', $this->file->getFilename());
    }

    /** @test */
    public function it_can_retrieve_the_full_path()
    {
        $this->filesystem->shouldReceive('getAdapter')
            ->once()
            ->andReturn($adapter = m::mock('League\Flysystem\AdapterInterface'));

        $adapter->shouldReceive('applyPathPrefix')
            ->with(__DIR__.'/files/test.png')
            ->once()
            ->andReturn(__DIR__.'/files/test.png');

        $this->assertEquals(__DIR__.'/files/test.png', $this->file->getFullPath());
    }

    /** @test */
    public function it_can_retrieve_the_image_size()
    {
        $this->filesystem->shouldReceive('getAdapter')
            ->once()
            ->andReturn($adapter = m::mock('League\Flysystem\AdapterInterface'));

        $this->filesystem->shouldReceive('getMimetype')
            ->once()
            ->andReturn('image/png');

        $adapter->shouldReceive('read')->once()
            ->with(__DIR__.'/files/test.png')
            ->andReturn(['contents' => file_get_contents(__DIR__.'/files/test.png')]);

        $size = [
            'width' => '1',
            'height' => '1',
        ];

        $this->assertEquals($size, $this->file->getImageSize());
    }

    /** @test */
    public function it_can_retrieve_the_file_extension()
    {
        $this->assertEquals('png', $this->file->getExtension());
    }
}
