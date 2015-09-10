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
use Cartalyst\Attributes\EntityTrait;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Attributes\EntityInterface;

class EntityTraitTest extends PHPUnit_Framework_TestCase
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
        $entity = new EntityStub;

        $this->assertInstanceOf('Cartalyst\Attributes\Relations\EavValues', $entity->values());
    }

    /** @test */
    public function it_can_create_a_new_value_model()
    {
        $entity = new EntityStub;

        $this->assertInstanceOf('Cartalyst\Attributes\Value', $entity->newValueModel());
    }

    /** @test */
    public function it_can_create_a_new_attribute_model()
    {
        $entity = new EntityStub;

        $this->assertInstanceOf('Cartalyst\Attributes\Attribute', $entity->newAttributeModel());
    }

    /** @test */
    public function it_can_save_entities()
    {
        $entity = new EntityStub;

        $entity->getConnection()->shouldReceive('transaction')->once();
        $entity->getConnection()->shouldReceive('rollBack')->once();
        $entity->getConnection()->shouldReceive('commit')->once();

        $entity->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->once();
        $entity->getConnection()->getQueryGrammar()->shouldReceive('compileInsertGetId')->once();

        $entity->getConnection()->getPostProcessor()->shouldReceive('processInsertGetId')->once();

        $entity->save();
    }

    /** @test */
    public function it_can_delete_existing_entities()
    {
        $entity = new EntityStub;
        $entity->exists = true;

        $entity->getConnection()->shouldReceive('transaction')->once()->andReturn(true);

        $this->assertTrue($entity->delete());
    }

    /** @test */
    public function it_returns_null_if_the_entity_does_not_exist()
    {
        $entity = new EntityStub;

        $entity->getConnection()->shouldReceive('transaction')->once();
        $entity->getConnection()->shouldReceive('rollBack')->once();
        $entity->getConnection()->shouldReceive('select')->once();
        $entity->getConnection()->shouldReceive('delete')->once();

        $entity->getConnection()->getQueryGrammar()->shouldReceive('compileDelete')->once();
        $entity->getConnection()->getQueryGrammar()->shouldReceive('compileSelect')->once();

        $entity->getConnection()->getPostProcessor()->shouldReceive('processSelect')->once()->andReturn([]);

        $this->assertNull($entity->delete());
    }

    /** @test */
    public function it_can_retrieve_the_value_relation()
    {
        $entity = new EntityStub;

        $this->assertEquals('values', $entity->getValueRelation());
    }

    /** @test */
    public function it_can_retrieve_the_available_attributes()
    {
        $entity = new EntityStub;
        $entity->exists = true;
        $entity->abc = 'foobar';

        $entity->getConnection()->getQueryGrammar()->shouldReceive('compileSelect')->once();

        $entity->getConnection()->shouldReceive('select')->once();

        $entity->getConnection()->getPostProcessor()->shouldReceive('processSelect')->once()->andReturn([]);

        $this->assertInstanceOf('Illuminate\Support\Collection', $entity::availableAttributes());
    }
}

class EntityStub extends Model implements EntityInterface
{

    use EntityTrait;
}
