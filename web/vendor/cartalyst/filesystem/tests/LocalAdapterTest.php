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
use Cartalyst\Filesystem\Adapters\LocalAdapter;

class LocalAdapterTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_connect()
    {
        $config = [
            'path' => __DIR__,
        ];

        $adapter = (new LocalAdapter)->connect($config);

        $this->assertInstanceOf('League\Flysystem\Adapter\Local', $adapter);
    }
}
