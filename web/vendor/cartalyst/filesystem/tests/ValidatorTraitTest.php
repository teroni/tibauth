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

use Cartalyst\Filesystem\Adapters\ValidatorTrait;
use PHPUnit_Framework_TestCase;

class ValidatorTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_invalid_argument_exception_if_required_fields_are_missing()
    {
        $adapter = [
            'adapter' => 'local',
        ];

        $validator = new ValidatorTraitTestStub($adapter);
    }
}

class ValidatorTraitTestStub
{

    use ValidatorTrait;

    protected $required = [
        'path',
    ];

    public function __construct($config)
    {
        return $this->validate($config);
    }
}
