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

class CollectionHandlerTest extends PHPUnit_Framework_TestCase
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

        $this->assertCount(count($expected = $this->getValidatedData()), $data = $handler->getData());

        foreach ($expected as $index => $item) {
            $this->assertEquals($item, $data[$index]);
        }
    }

    public function testTotalCount()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());
        $handler->prepareTotalCount();

        $this->assertSame(count($dataGrid->getData()), $handler->getTotalCount());
    }

    public function testPreparingSelect()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());
        $handler->prepareSelect();

        $expected = array_map(function ($item) {
            unset($item['last_name']);
            $item['sex'] = $item['gender'];
            unset($item['gender']);
            return $item;
        }, $this->getValidatedData());

        $this->assertEquals($expected, $handler->getData()->all());
    }

    public function testFilteredCount()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => 'B'),
                array('sex'        => 'male'),
            )
        );

        $handler->prepareFilters();
        $handler->prepareFilteredCount();

        $expected = $this->getValidatedData();
        $this->assertEquals(2, $handler->getFilteredCount());
    }

    public function testHydrateResults()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => 'B'),
                array('sex'        => 'male'),
            )
        );

        $handler->prepareFilters();
        $handler->prepareFilteredCount();
        $handler->hydrate();

        $expected = $this->getValidatedData();

        $this->assertEquals(2, $handler->getFilteredCount());
        $this->assertCount(2, $data = $handler->getResults());
        $this->assertEquals(array($expected[0], $expected[2]), array_values($data));
    }

    public function testHydrateMaxResults()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => 'B'),
                array('sex'        => 'male'),
            )
        );

        $handler->prepareFilters();
        $handler->prepareFilteredCount();
        $handler->hydrate(1);

        $expected = $this->getValidatedData();

        $this->assertCount(1, $data = $handler->getResults());
        $this->assertEquals(array($expected[0]), array_values($data));
    }

    public function testColumnFilters()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => 'B'),
                array('sex'        => 'male'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(2, $data = $handler->getData());
        $this->assertEquals(array($expected[0], $expected[2]), array_values($data->all()));
    }

    public function testColumnFilters1()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid(true));

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('address' => 'Br'),
                array('sex'     => 'male'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData(true);
        $expected = $expected[2];

        $this->assertCount(1, $data = $handler->getData());
        $this->assertEquals($expected, head(array_values($data->all())));
    }

    public function testNullColumnFilters()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => 'null'),
                array('sex'        => 'male'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(0, $data = $handler->getData());
        $this->assertEmpty(array_values($data->all()));
    }

    public function testColumnFiltersWithAliases()
    {
        $expected = array(
            array(
                'first_name' => 'Ben',
                'last_name'  => 'Corlett',
                'gender'     => 'male',
                'sortable'   => 'foo-1',
                'age'        => 22,
            ),
        );

        $dataGrid = m::mock('Cartalyst\DataGrid\DataGrid');
        $dataGrid->shouldReceive('getData')->andReturn($expected);
        $dataGrid->shouldReceive('getEnvironment')->andReturn($environment = m::mock('Cartalyst\DataGrid\Environment'));
        $environment->shouldReceive('getRequestProvider')->andReturn(m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

        $columns = array(
            'first_name',
            'gender' => 'gender',
            'sortable',
            'age',
        );

        $dataGrid->shouldReceive('getColumns')->andReturn($columns);

        $handler = new Handler($dataGrid);

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('gender' => 'male'),
            )
        );

        $handler->prepareFilters();

        $this->assertCount(1, $data = $handler->getData());
        $this->assertEquals($expected[0], head(array_values($data->all())));
    }

    public function testOperatorFilters1()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|>=21|<=25|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(4, $data = $handler->getData());
        $this->assertEquals(array($expected[0], $expected[2], $expected[3], $expected[4]), array_values($data->all()));
    }

    public function testOperatorFilters2()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|<21|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(1, $data = $handler->getData());
        $this->assertEquals(array($expected[5]), array_values($data->all()));
    }

    public function testOperatorFilters3()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|>20|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(5, $data = $handler->getData());
        $this->assertEquals(array($expected[0], $expected[1], $expected[2], $expected[3], $expected[4]), array_values($data->all()));
    }

    public function testOperatorFilters4()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|<>20|!=21|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(4, $data = $handler->getData());
        $this->assertEquals(array($expected[0], $expected[1], $expected[2], $expected[3]), array_values($data->all()));
    }

    public function testOperatorFilters5()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid(true));

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('address..city' => 'Ben-city'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData(true);

        $this->assertCount(1, $data = $handler->getData(true));

        $this->assertEquals(array($expected[0]), array_values($data->all()));
    }

    public function testOperatorFilters6()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid(true));

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array()
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData(true);

        $this->assertCount(6, $data = $handler->getData(true));

        $this->assertEquals(array($expected[0], $expected[1], $expected[2], $expected[3], $expected[4], $expected[5]), array_values($data->all()));
    }

    public function testOperatorFilters7()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|<>20|=21|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(1, $data = $handler->getData());
        $this->assertEquals(array($expected[4]), array_values($data->all()));
    }

    public function testOperatorFilters8()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('age' => '|===21|'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(0, $data = $handler->getData());
        $this->assertEquals(array(), array_values($data->all()));
    }

    public function testOperatorFilters9()
    {
        $dataGrid = m::mock('Cartalyst\DataGrid\DataGrid');

        $data = array(
            array(
                'first_name' => 'Ben',
                'created_at' => '2014-01-01 00:00:00',
            ),
            array(
                'first_name' => 'Dan',
                'created_at' => '2014-02-21 23:00:00',
            ),
            array(
                'first_name' => 'Bruno',
                'created_at' => '2014-03-04 16:30:00',
            ),
            array(
                'first_name' => 'Jared',
                'created_at' => '2013-03-11 12:10:00',
            ),
            array(
                'first_name' => 'Clarissa',
                'created_at' => '2013-05-21 00:20:00',
            ),
            array(
                'first_name' => 'Jessica',
                'created_at' => '2013-12-10 21:20:00',
            ),
        );

        $dataGrid->shouldReceive('getData')->andReturn($data);
        $dataGrid->shouldReceive('getEnvironment')->andReturn($environment = m::mock('Cartalyst\DataGrid\Environment'));
        $environment->shouldReceive('getRequestProvider')->andReturn(m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

        $columns = array(
            'first_name',
            'created_at',
        );

        $dataGrid->shouldReceive('getColumns')->andReturn($columns);

        $handler = new Handler($dataGrid);

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('created_at' => '|>=2014-01-01 00:00:00|<=2014-02-01 00:00:00|'),
            )
        );

        $handler->prepareFilters();

        $expected = array(
                'first_name' => 'Ben',
                'created_at' => '2014-01-01 00:00:00',
        );

        $this->assertCount(1, $data = $handler->getData());

        $this->assertEquals(array($expected), array_values($data->all()));
    }

    public function testRegexFilter()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array(
                array('first_name' => '/^.*?e.*?$/'),
            )
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(3, $data = $handler->getData());
        $this->assertEquals(array($expected[0], $expected[3], $expected[5]), array_values($data->all()));
    }

    public function testGlobalFilters()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array('me')
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(2, $data = $handler->getData());
        $this->assertEquals(array($expected[1], $expected[4]), array_values($data->all()));
    }

    public function testGlobalFiltersNoResults()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array('meaw')
        );

        $handler->prepareFilters();

        $expected = $this->getValidatedData();
        $this->assertCount(0, $data = $handler->getData());
        $this->assertEmpty(array_values($data->all()));
    }

    public function testGlobalFiltersArray()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid(true));

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getFilters')->once()->andReturn(array('Ben-city')
        );

        $handler->prepareFilters();

        $expected = head($this->getValidatedData(true));

        $this->assertCount(1, $data = $handler->getData());
        $this->assertEquals(head(array_values($data->all())), $expected);
    }

    public function testPrepareSortAscending()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once()->andReturn('first_name');
        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getDirection')->once()->andReturn('asc');

        $handler->prepareSort();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getData());
        $ordered = array(0, 2, 4, 1, 3, 5);

        foreach (array_values($data->all()) as $index => $item) {
            $this->assertEquals($expected[$ordered[$index]], $item);
        }
    }

    public function testPrepareSortDescending()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once()->andReturn('first_name');
        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getDirection')->once()->andReturn('desc');

        $handler->prepareSort();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getData());
        $ordered = array(5, 3, 1, 4, 2, 0);

        foreach (array_values($data->all()) as $index => $item) {
            $this->assertEquals($expected[$ordered[$index]], $item);
        }
    }

    public function testPrepareSortWithAlias()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once()->andReturn('sex');
        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getDirection')->once()->andReturn('desc');

        $handler->prepareSort();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getData());
        $ordered = array(3, 0, 2, 1, 5, 4);

        foreach (array_values($data->all()) as $index => $item) {
            $this->assertEquals($expected[$ordered[$index]], $item);
        }
    }

    public function testPrepareSortNaturally()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once()->andReturn('sortable');
        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getDirection')->once()->andReturn('asc');

        $handler->prepareSort();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getData());
        $ordered = array(0, 5, 1, 4, 2, 3);

        foreach (array_values($data->all()) as $index => $item) {
            $this->assertEquals($expected[$ordered[$index]], $item);
        }
    }

    public function testPrepareSortWithoutColumn()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once();

        $handler->prepareSort();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getData());
        $ordered = array(0, 1, 2, 3, 4, 5);

        foreach (array_values($data->all()) as $index => $item) {
            $this->assertEquals($expected[$ordered[$index]], $item);
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testPrepareSortWhereColumnDoesntExist()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getSort')->once()->andReturn('invalid');

        $handler->prepareSort();
    }

    public function testTransform()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $handler->setTransformer(function ($el) {
            $el['first_name'] = 'foobar';

            return $el;
        });

        $handler->hydrate();

        $expected = $this->getValidatedData();
        $this->assertCount(count($expected), $data = $handler->getResults());

        foreach ($data as $item) {
            $this->assertEquals($item['first_name'], 'foobar');
        }
    }

    public function testPreparePagination()
    {
        $dataGrid = $this->getMockDataGrid();

        $handler = m::mock('Cartalyst\DataGrid\DataHandlers\CollectionHandler[calculatePagination]', array($dataGrid));

        $request = $dataGrid->getEnvironment()->getRequestProvider();

        // Just need these here even though we're mocking the method
        $request->shouldReceive('getMethod')->once()->andReturn('group');
        $request->shouldReceive('getThreshold')->once()->andReturn(100);
        $request->shouldReceive('getThrottle')->once()->andReturn(100);

        $handler->shouldReceive('calculatePagination')->once()->andReturn(array(3, 2));
        $dataGrid->getEnvironment()->getRequestProvider()->shouldReceive('getPage')->once()->andReturn(2);

        $handler->setFilteredCount(6);
        $handler->preparePagination();

        $expected = $this->getValidatedData();

        $this->assertCount(2, $data = $handler->getData());
        $this->assertEquals($expected[2], $data[0]);
        $this->assertEquals($expected[3], $data[1]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidateDataThrowsExceptionIfDataIsNotACollection()
    {
        $handler = new Handler($dataGrid = $this->getMockDataGrid());

        $handler->validateData('test');
    }

    protected function getMockDataGrid($nested = false)
    {
        $dataGrid = m::mock('Cartalyst\DataGrid\DataGrid');
        $dataGrid->shouldReceive('getData')->andReturn($this->getData($nested));
        $dataGrid->shouldReceive('getEnvironment')->andReturn($environment = m::mock('Cartalyst\DataGrid\Environment'));
        $environment->shouldReceive('getRequestProvider')->andReturn(m::mock('Cartalyst\DataGrid\RequestProviders\ProviderInterface'));

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
                        'street' => array('name' => 'foo-street'),
                        'city'   => $value->first_name . '-city',
                    );
                } elseif (is_array($value)) {
                    $value['address'] = array(
                        'street' => array('name' => 'foo-street'),
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
                    'street' => array('name' => 'foo-street'),
                    'city'   => $value['first_name'] . '-city',
                );
            }
        }

        return $data;
    }
}
