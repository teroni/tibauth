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
use DataHandlerStub;
use PHPUnit_Framework_TestCase;
use Cartalyst\DataGrid\DataGrid;
use Cartalyst\DataGrid\RequestProviders\Provider;
use Cartalyst\DataGrid\DataHandlers\HandlerInterface;

class DataGridTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup resources and dependencies.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        require_once __DIR__.'/stubs/DataHandlerStub.php';
    }

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
     * @expectedException RuntimeException
     */
    public function testCreatingDataHandlerFailsWithNoMappings()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array());
        $env->shouldReceive('getDataHandlerMappings')->andReturn(array());
        $dataGrid->createDataHandler();
    }

    public function testCreatingDataHandler()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array('bar'), array());
        $env->shouldReceive('getDataHandlerMappings')->andReturn(array(
            'DataHandlerStub' => function (array $data) {
                return (count($data) == 1);
            }
        ));

        $this->assertInstanceOf('DataHandlerStub', $dataGrid->createDataHandler());
    }

    public function testRetrievingDataHandler()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array('bar'), array());
        $env->shouldReceive('getDataHandlerMappings')->andReturn(array(
            'DataHandlerStub' => function (array $data) {
                return (count($data) == 1);
            }
        ));

        $this->assertInstanceOf('DataHandlerStub', $dataGrid->createDataHandler());
        $this->assertInstanceOf('DataHandlerStub', $dataGrid->getDataHandler());
    }

    public function testCasting()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array());
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')
            ->times(3)
            ->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));

        $request->shouldReceive('getThrottle')
            ->times(3);

        $request->shouldReceive('getThreshold')
            ->times(3);

        $expected = array(
            'total'          => 100,
            'filtered'       => 20,
            'throttle'       => null,
            'threshold'      => null,
            'page'           => 1,
            'pages'          => 2,
            'previous_page'  => null,
            'next_page'      => 2,
            'per_page'       => 10,
            'sort'           => 'id',
            'direction'      => 'asc',
            'default_column' => null,
            'results'        => array('foo', 'bar'),
        );

        $this->assertEquals($expected, $dataGrid->toArray());
        $this->assertEquals($expectedJson = json_encode($expected), $dataGrid->toJson());
        $this->assertEquals($expectedJson, (string) $dataGrid);
    }

    public function testOverrideThrottle()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array(), array('throttle' => 10));
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));
        $env->shouldReceive('getDataHandlerMappings')->once()->andReturn(array(
        'Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
                    return (
                        $data instanceof Collection or
                        is_array($data)
                    );
                }
        ));

        $request->shouldReceive('getSort')->twice()->andReturn(null);
        $request->shouldReceive('getDirection')->once()->andReturn(null);
        $request->shouldReceive('setDefaultThrottle')->once()->with(10);
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('getDownload')->once()->andReturn(false);

        $dataGrid->setupDataGridContext();
    }

    public function testOverrideThreshold()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array(), array('threshold' => 10));
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));
        $env->shouldReceive('getDataHandlerMappings')->once()->andReturn(array(
        'Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
                    return (
                        $data instanceof Collection or
                        is_array($data)
                    );
                }
        ));

        $request->shouldReceive('getSort')->twice()->andReturn(null);
        $request->shouldReceive('getDirection')->once()->andReturn(null);
        $request->shouldReceive('setDefaultThreshold')->once()->with(10);
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('getDownload')->once()->andReturn(false);

        $dataGrid->setupDataGridContext();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDownloadJson()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array());
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));
        $env->shouldReceive('getDataHandlerMappings')->once()->andReturn(array(
        'Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
                    return (
                        $data instanceof Collection or
                        is_array($data)
                    );
                }
        ));

        $request->shouldReceive('getSort')->twice()->andReturn(null);
        $request->shouldReceive('getDirection')->once()->andReturn(null);
        $request->shouldReceive('getDownload')->once()->andReturn('json');
        $request->shouldReceive('getMaxResults')->once();
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('downloadJson')->once();

        $dataGrid->setupDataGridContext();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDownloadCsv()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array());
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));
        $env->shouldReceive('getDataHandlerMappings')->once()->andReturn(array(
        'Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
                    return (
                        $data instanceof Collection or
                        is_array($data)
                    );
                }
        ));

        $request->shouldReceive('getSort')->twice()->andReturn(null);
        $request->shouldReceive('getDirection')->once()->andReturn(null);
        $request->shouldReceive('getDownload')->once()->andReturn('csv');
        $request->shouldReceive('getMaxResults')->once();
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('downloadCsv')->once();

        $dataGrid->setupDataGridContext();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDownloadPdf()
    {
        $dataGrid = new DataGrid($env = $this->getMockEnvironment(), array(), array());
        $dataGrid->setDataHandler($this->getMockHandler());

        $env->shouldReceive('getRequestProvider')->andReturn($request = m::mock('Cartalyst\DataGrid\RequestProviders\Provider'));
        $env->shouldReceive('getDataHandlerMappings')->once()->andReturn(array(
        'Cartalyst\DataGrid\DataHandlers\CollectionHandler' => function ($data) {
                    return (
                        $data instanceof Collection or
                        is_array($data)
                    );
                }
        ));

        $request->shouldReceive('getSort')->twice()->andReturn(null);
        $request->shouldReceive('getDirection')->once()->andReturn(null);
        $request->shouldReceive('getDownload')->once()->andReturn('pdf');
        $request->shouldReceive('getMaxResults')->once();
        $request->shouldReceive('getFilters')->once()->andReturn(array());
        $request->shouldReceive('downloadPdf')->once();

        $dataGrid->setupDataGridContext();
    }

    protected function getMockEnvironment()
    {
        $environment = m::mock('Cartalyst\DataGrid\Environment');

        return $environment;
    }

    protected function getMockHandler()
    {
        $handler = m::mock('Cartalyst\DataGrid\DataHandlers\HandlerInterface');

        $handler->shouldReceive('getTotalCount')->andReturn(100);
        $handler->shouldReceive('getFilteredCount')->andReturn(20);
        $handler->shouldReceive('getPage')->andReturn(1);
        $handler->shouldReceive('getPagesCount')->andReturn(2);
        $handler->shouldReceive('getPreviousPage');
        $handler->shouldReceive('getNextPage')->andReturn(2);
        $handler->shouldReceive('getPerPage')->andReturn(10);
        $handler->shouldReceive('getSort')->andReturn('id');
        $handler->shouldReceive('getDirection')->andReturn('asc');
        $handler->shouldReceive('getDefaultColumn');
        $handler->shouldReceive('getResults')->andReturn(array('foo', 'bar'));

        return $handler;
    }
}
