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
use PHPUnit_Framework_TestCase;
use Cartalyst\Attributes\Attribute;

class AttributeTest extends PHPUnit_Framework_TestCase
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
    public function it_has_a_values_relationship()
    {
        $attribute = new Attribute;

        $this->addMockConnection($attribute);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $attribute->values());
    }

    /** @test */
    public function it_can_retrieve_the_attribute_key()
    {
        $attribute = new Attribute;

        $attribute->slug = 'test';

        $this->assertEquals('test', $attribute->getAttributeKey());
    }

    /** @test */
    public function it_can_retrieve_the_attribute_key_name()
    {
        $attribute = new Attribute;

        $this->assertEquals('slug', $attribute->getAttributeKeyName());
    }

    /** @test */
    public function it_can_retrieve_the_value_relation()
    {
        $attribute = new Attribute;

        $this->assertEquals('values', $attribute->getValueRelation());
    }

    /** @test */
    public function it_can_create_a_new_value_model()
    {
        $attribute = new Attribute;

        $this->assertInstanceOf('Cartalyst\Attributes\Value', $attribute->newValueModel());
    }

    /** @test */
    public function it_can_create_a_new_entity_model()
    {
        $attribute = new Attribute;

        $this->assertInstanceOf('Cartalyst\Attributes\Value', $attribute->newEntityModel());
    }

    /** @test */
    public function it_can_delete_an_attribute()
    {
        $attribute = m::mock('Cartalyst\Attributes\Attribute[values]');
        $attribute->shouldReceive('values')->once()->andReturn($related = m::mock('Illuminate\Database\Eloquent\Relations\HasMany'));
        $related->shouldReceive('delete')->once();

        $attribute->delete();
    }


    /**
     * Adds a mock connection to the object.
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
