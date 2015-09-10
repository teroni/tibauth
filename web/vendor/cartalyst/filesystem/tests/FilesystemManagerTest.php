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

use Cartalyst\Filesystem\FilesystemManager;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class FilesystemManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the filesystem manager instance.
     *
     * @var \Cartalyst\Filesystem\FilesystemManager
     */
    protected $filesystem;

    /**
     * Holds the filesystem config.
     *
     * @var array
     */
    protected $config;

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
        $this->config = require __DIR__.'/../src/config/config.php';

        $this->filesystem = new FilesystemManager($this->config);

        $this->filesystem->setMaxFileSize(10); // 10 MB

        $this->filesystem->setAllowedMimes([
            'image/jpeg',
        ]);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $filesystem = new FilesystemManager([]);

        $this->assertInstanceOf('Cartalyst\Filesystem\FilesystemManager', $filesystem);
    }

    /** @test */
    public function it_uses_local_as_default_connection()
    {
        $this->assertEquals('local', $this->filesystem->getDefaultConnection());
    }

    /** @test */
    public function it_can_switch_connections()
    {
        $this->filesystem->setDefaultConnection('dropbox');

        $this->assertEquals('dropbox', $this->filesystem->getDefaultConnection());
    }

    /** @test */
    public function it_can_set_and_retrieve_defaults()
    {
        $this->assertInstanceOf('Cartalyst\Filesystem\Filesystem', $this->filesystem->connection());


        $this->filesystem->setMaxFileSize(20);
        $this->assertEquals(20, $this->filesystem->getMaxFileSize());


        $mimes = [
            'audio/ogg',
            'image/jpeg',
        ];
        $this->filesystem->setAllowedMimes($mimes);
        $this->assertSame($mimes, $this->filesystem->getAllowedMimes());


        $placeholders = [
            ':yyyy' => date('Y'),
            ':yy'   => date('y'),
        ];
        $this->filesystem->setPlaceholders($placeholders);
        $this->assertSame($placeholders, $this->filesystem->getPlaceholders());


        $dispersion = ':yyyy/:mm';
        $this->filesystem->setDispersion($dispersion);
        $this->assertSame($dispersion, $this->filesystem->getDispersion());


        $connection = 'dropbox';
        $this->filesystem->setDefaultConnection($connection);
        $this->assertSame($connection, $this->filesystem->getDefaultConnection());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_invalid_argument_exception_on_invalid_connections()
    {
        $this->assertInstanceOf('Cartalyst\Filesystem\Filesystem', $this->filesystem->connection('invalid'));
    }

    /** @test */
    public function it_dynamically_passes_methods_to_the_connection()
    {
        $this->filesystem = new FilesystemManager($this->config, $connection = m::mock('Cartalyst\Filesystem\ConnectionFactory'));

        $connection->shouldReceive('make')->once()->andReturn($filesystem = m::mock('Cartalyst\Filesystem\Filesystem'));

        $filesystem->shouldReceive('write')->once();

        $this->filesystem->write('test', 'content');
    }
}
