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

use stdClass;
use Mockery as m;
use ArrayableStub;
use PHPUnit_Framework_TestCase;
use Cartalyst\DataGrid\DataHandlers\CollectionHandler as Handler;

class BaseHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup resources and dependencies.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        require_once __DIR__.'/stubs/ArrayableStub.php';
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

    public function testDataIsTransformed()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $handler->setUpDataHandlerContext();
        $handler->setSort('first_name');
        $handler->setDirection('asc');
        $handler->setDefaultColumn('age');
        $handler->setTransformer(function ($el) {
            $el['first_name'] = 'Name';
        });

        $this->assertEquals($handler->getTotalCount(), 6);
        $this->assertEquals($handler->getFilteredCount(), 6);
        $this->assertEquals($handler->getPage(), 2);
        $this->assertEquals($handler->getPagesCount(), 3);
        $this->assertEquals($handler->getPreviousPage(), 1);
        $this->assertEquals($handler->getNextPage(), 3);
        $this->assertEquals($handler->getPerPage(), 2);
        $this->assertEquals($handler->getSort(), 'first_name');
        $this->assertEquals($handler->getDirection(), 'asc');
        $this->assertEquals($handler->getDefaultColumn(), 'age');
        $this->assertInstanceOf('Closure', $handler->getTransformer());
        $this->assertCount(2, $handler->getResults());
    }

    public function testCalculatePages()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $handler->setUpDataHandlerContext();
        $handler->setSort('first_name');
        $handler->setDirection('asc');
        $handler->setDefaultColumn('age');
        $handler->setTransformer(function ($el) {
            $el['first_name'] = 'Name';
        });

        $pages = $handler->calculatePages(19, 6, 9);

        $this->assertEquals($pages[0], 6);
        $this->assertEquals($pages[1], 3);
        $this->assertNull($pages[2]);
    }

    public function testMaxResults()
    {
        $dataGrid = m::mock('Cartalyst\DataGrid\DataGrid');
        $dataGrid->shouldReceive('getData')->andReturn($this->getData());
        $dataGrid->shouldReceive('getEnvironment')->andReturn($environment = m::mock('Cartalyst\DataGrid\Environment'));
        $environment->shouldReceive('getRequestProvider')->andReturn($requestProvider = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

        $requestProvider->shouldReceive('getFilters')->once()->andReturn(array());
        $requestProvider->shouldReceive('getSort')->once()->andReturn('first_name');
        $requestProvider->shouldReceive('getDirection')->once()->andReturn('asc');

        $columns = array(
            'first_name',
            'gender' => 'sex',
            'sortable',
            'age',
        );

        $dataGrid->shouldReceive('getColumns')->andReturn($columns);

        $handler = new Handler($dataGrid);

        $handler->setUpDataHandlerContext(false, 1);

        $this->assertCount(1, $handler->getResults());
    }

    protected function getMockDataGrid($nested = false)
    {
        $dataGrid = m::mock('Cartalyst\DataGrid\DataGrid');
        $dataGrid->shouldReceive('getData')->andReturn($this->getData($nested));
        $dataGrid->shouldReceive('getEnvironment')->andReturn($environment = m::mock('Cartalyst\DataGrid\Environment'));
        $environment->shouldReceive('getRequestProvider')->andReturn($requestProvider = m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

        $requestProvider->shouldReceive('getFilters')->once()->andReturn(array());
        $requestProvider->shouldReceive('getSort')->once()->andReturn('first_name');
        $requestProvider->shouldReceive('getDirection')->once()->andReturn('asc');
        $requestProvider->shouldReceive('getMethod')->once()->andReturn('single');
        $requestProvider->shouldReceive('getThreshold')->once()->andReturn(2);
        $requestProvider->shouldReceive('getThrottle')->once()->andReturn(2);
        $requestProvider->shouldReceive('getPage')->once()->andReturn(2);

        $columns = array(
            'first_name',
            'gender' => 'sex',
            'sortable',
            'age',
        );

        if ($nested) {
            array_push($columns, 'address');
        }

        $dataGrid->shouldReceive('getColumns')->andReturn($columns);
        return $dataGrid;
    }

    protected function getData($nested = false)
    {
        $object1 = new StdClass;
        $object1->first_name = 'Ben';
        $object1->last_name  = 'Corlett';
        $object1->gender     = 'male';
        $object1->sortable   = 'foo-1';
        $object1->age        = 22;

        $data = array(
            $object1,
            new ArrayableStub($nested),
            array(
                'first_name' => 'Bruno',
                'last_name'  => 'Gaspar',
                'gender'     => 'male',
                'sortable'   => 'foo-100',
                'age'        => 25,
            ),
            array(
                'first_name' => 'Jared',
                'last_name'  => 'West',
                'gender'     => 'male',
                'sortable'   => 'foo-101',
                'age'        => 24,
            ),
            array(
                'first_name' => 'Clarissa',
                'last_name'  => 'Syme',
                'gender'     => 'female',
                'sortable'   => 'foo-20',
                'age'        => 21,
            ),
            array(
                'first_name' => 'Jessica',
                'last_name'  => 'Hick',
                'gender'     => 'female',
                'sortable'   => 'foo-3',
                'age'        => 20,
            ),
        );

        if ($nested) {
            foreach ($data as &$value) {
                if ($value instanceof stdClass) {
                    $value->address = array(
                        'street' => 'foo-street',
                        'city'   => $value->first_name . '-city',
                    );
                } elseif (is_array($value)) {
                    $value['address'] = array(
                        'street' => 'foo-street',
                        'city'   => $value['first_name'] . '-city',
                    );
                }
            }
        }

        return $data;
    }

    public function getValidatedData($nested = false)
    {
        $data = array(
            array(
                'first_name' => 'Ben',
                'last_name'  => 'Corlett',
                'gender'     => 'male',
                'sortable'   => 'foo-1',
                'age'        => 22,
            ),
            array(
                'first_name' => 'Dan',
                'last_name'  => 'Syme',
                'gender'     => 'male',
                'sortable'   => 'foo-13',
                'age'        => 30,
            ),
            array(
                'first_name' => 'Bruno',
                'last_name'  => 'Gaspar',
                'gender'     => 'male',
                'sortable'   => 'foo-100',
                'age'        => 25,
            ),
            array(
                'first_name' => 'Jared',
                'last_name'  => 'West',
                'gender'     => 'male',
                'sortable'   => 'foo-101',
                'age'        => 24,
            ),
            array(
                'first_name' => 'Clarissa',
                'last_name'  => 'Syme',
                'gender'     => 'female',
                'sortable'   => 'foo-20',
                'age'        => 21,
            ),
            array(
                'first_name' => 'Jessica',
                'last_name'  => 'Hick',
                'gender'     => 'female',
                'sortable'   => 'foo-3',
                'age'        => 20,
            ),
        );

        if ($nested) {
            foreach ($data as &$value) {
                $value['address'] = array(
                    'street' => 'foo-street',
                    'city'   => $value['first_name'] . '-city',
                );
            }
        }

        return $data;
    }
}
