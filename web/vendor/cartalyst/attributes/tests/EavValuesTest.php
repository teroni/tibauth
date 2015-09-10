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
use Cartalyst\Attributes\Relations\EavValues;
use PHPUnit_Framework_TestCase;

class EavValuesTest extends PHPUnit_Framework_TestCase
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
    public function it_can_match()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $model = m::mock('Illuminate\Database\Eloquent\Model');

        $id       = 'key';
        $localKey = 'foo';
        $relation = 'foo';
        $type     = 'foo';

        $models  = [$model];
        $results = new \Illuminate\Database\Eloquent\Collection([$model]);

        $query->shouldReceive('getModel')
            ->once()
            ->andReturn($model);

        $query->shouldReceive('where')
            ->with('key', '=', 'bar')
            ->once();

        $query->shouldReceive('where')
            ->with('foo', '')
            ->once();

        $query->shouldReceive('whereNotNull');

        $model->shouldReceive('getMorphClass')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('foo')
            ->once()
            ->andReturn('bar');

        $eavValues = new EavValues($query, $model, $type, $id, $localKey);

        $model->shouldReceive('getKey')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('key')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('newCollection')
            ->with([$model])
            ->once()
            ->andReturn($results);

        $model->shouldReceive('setRelation')
            ->once();

        $model->shouldReceive('getAttributeRelation')
            ->once();

        $model->shouldReceive('getRelation')
            ->once()
            ->andReturn($relation = m::mock('Illuminate\Database\Eloquent\Relations\Relation'));

        $relation->shouldReceive('getAttributeKey')
            ->once();

        $model->shouldReceive('getValueKey')
            ->once();

        $model->shouldReceive('setAttribute')
            ->once();

        $eavValues->match($models, $results, $relation);
    }
}
