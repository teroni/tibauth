<?php

/**
 * Part of the Attributes package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Attributes
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Attributes\Tests;

use Mockery as m;
use Cartalyst\Attributes\Value;
use PHPUnit_Framework_TestCase;

class ValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_has_a_attribute_relationship()
    {
        $value = new Value;

        $this->addMockConnection($value);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $value->attribute());
    }

    /** @test */
    public function it_has_a_entity_relationship()
    {
        $value = new Value;

        $this->addMockConnection($value);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $value->entity());
    }

    /** @test */
    public function it_can_set_and_get_the_value_key()
    {
        $value = new Value;

        $value->setValueKey('foobar');

        $this->assertEquals('foobar', $value->getValueKey());
    }

    /** @test */
    public function it_can_create_a_new_attribute_model()
    {
        $value = new Value;

        $this->assertInstanceOf('Cartalyst\Attributes\Attribute', $value->newAttributeModel());
    }

    /** @test */
    public function it_can_create_a_new_entity_model()
    {
        $value = new Value;

        $this->assertInstanceOf('Cartalyst\Attributes\Value', $value->newEntityModel());
    }

    /** @test */
    public function it_can_retrieve_the_attribute_relation()
    {
        $value = new Value;

        $this->assertEquals('attribute', $value->getAttributeRelation());
    }

    /** @test */
    public function it_can_retrieve_the_entity_relation()
    {
        $value = new Value;

        $this->assertEquals('entity', $value->getEntityRelation());
    }

    /**
     * Adds a mock connection to the model.
     *
     * @param  mixed
     * @return void
     */
    protected function addMockConnection($model)
    {
        $model->setConnectionResolver($resolver = m::mock('Illuminate\Database\ConnectionResolverInterface'));
        $resolver->shouldReceive('connection')->andReturn(m::mock('Illuminate\Database\Connection'));
        $model->getConnection()->shouldReceive('getQueryGrammar')->andReturn(m::mock('Illuminate\Database\Query\Grammars\Grammar'));
        $model->getConnection()->shouldReceive('getPostProcessor')->andReturn(m::mock('Illuminate\Database\Query\Processors\Processor'));
    }
}
