<?php

/**
 * Part of the Data Grid package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Data Grid
 * @version    3.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\DataGrid\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Collection;
use Cartalyst\DataGrid\Environment;

class EnvironmentTest extends PHPUnit_Framework_TestCase
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

    public function testRequestProviderIsAbleToBeOverridden()
    {
        $environment = new Environment($requestProvider = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

        $this->assertEquals($requestProvider, $environment->getRequestProvider());
        $environment->setRequestProvider($requestProvider2 = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));
        $this->assertEquals($requestProvider2, $environment->getRequestProvider());
        $this->assertNotSame($requestProvider, $environment->getRequestProvider());
    }

    /**
     * @runInSeparateProcess
     */
    public function testMakeSetsUpDataGridContext()
    {
        $request = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface');

        $request->shouldReceive('getSort')->twice();
        $request->shouldReceive('getDirection')->once();
        $request->shouldReceive('getDownload')->once();
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('getMethod')->once()->andReturn('single');
        $request->shouldReceive('getThreshold')->twice()->andReturn(100);
        $request->shouldReceive('getThrottle')->twice()->andReturn(100);
        $request->shouldReceive('getPage')->once()->andReturn(1);

        $environment  = new Environment($request);
        $environment->setDataHandlerMapping('Cartalyst\DataGrid\DataHandlers\CollectionHandler', function ($data) {
            return (
                $data instanceof Collection or
                is_array($data)
            );
        });

        $dataGridMock = m::mock('Cartalyst\DataGrid\DataGrid');
        $dataGrid     = $environment->make(array(array('foo' => 'bar')), array('foo'));

        $dataGridMock->shouldReceive('getEnvironment')->andReturn($environment);
        $dataGridMock->shouldReceive('getData')->andReturn(array(
            'total'          => 1,
            'filtered'       => 1,
            'throttle'       => 100,
            'threshold'      => 100,
            'page'           => 1,
            'pages'          => 1,
            'previous_page'  => null,
            'next_page'      => null,
            'per_page'       => 1,
            'sort'           => null,
            'direction'      => null,
            'default_column' => null,
            'results'        => array(array('foo' => 'bar')),
        ));

        $expected = array('Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
            return (
                $data instanceof Collection or
                is_array($data)
            );
        });

        $this->assertEquals($expected, $environment->getDataHandlerMappings());
        $this->assertEquals($dataGridMock->getData(), $dataGrid->toArray());
    }

    public function testSetDataHandlerMappings()
    {
        $request = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface');

        $mappings = ['Cartalyst\DataGrid\DataHandlers\CollectionHandler', function ($data) {
            return (
                $data instanceof Collection or
                is_array($data)
            );
        }];

        $environment  = new Environment($request);
        $environment->setDataHandlerMappings($mappings);

        $this->assertSame($mappings, $environment->getDataHandlerMappings());
    }
}
