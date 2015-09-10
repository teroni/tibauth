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

namespace Cartalyst\DataGrid;

use Closure;
use RuntimeException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Cartalyst\DataGrid\DataHandlers\HandlerInterface as DataHandlerInterface;

class DataGrid implements Arrayable, Jsonable
{
    /**
     * The data grid environment.
     *
     * @var \Cartalyst\DataGrid\Environment
     */
    protected $env;

    /**
     * The data object passed to the datagrid, used for
     * manipulation and returning of results.
     *
     * @var mixed
     */
    protected $data;

    /**
     * The data source, responsible for returning the
     * appropriate information from the data provided.
     *
     * @var \Cartalyst\DataGrid\DataHandler\HandlerInterface
     */
    protected $dataHandler;

    /**
     * Array of columns presented in data-grid. The values of
     * this array should match the properties (or indexes)
     * of each result returned from the data.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Array of settings.
     *
     * @var array
     */
    protected $settings = array();

    /**
     * The transformer closure.
     *
     * @var \Closure
     */
    protected $transformer;

    /**
     * Creates a new data grid object.
     *
     * @param  \Cartalyst\DataGrid\Environment  $env
     * @param  mixed  $data
     * @param  array  $columns
     * @param  array  $settings
     * @param  \Closure  $transformer
     * @return void
     */
    public function __construct(Environment $env, $data, array $columns, array $settings = array(), Closure $transformer = null)
    {
        $this->env = $env;

        $this->data = $data;

        $this->columns = $columns;

        $this->settings = $settings;

        $this->transformer = $transformer;
    }

    /**
     * Sets up the data grid context (with filters, ordering,
     * searching etc).
     *
     * This method simply calls a bunch of other methods. The
     * way SQL works means the order we call the methods in
     * matters.
     *
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function setupDataGridContext()
    {
        $this->dataHandler = $this->createDataHandler();

        $requestProvider = $this->env->getRequestProvider();

        $sort = $requestProvider->getSort() ?: array_get($this->settings, 'sort');

        $direction = $requestProvider->getDirection() ?: array_get($this->settings, 'direction');

        $this->dataHandler->setSort($sort);

        $this->dataHandler->setDirection($direction);

        $this->dataHandler->setDefaultColumn(array_get($this->settings, 'sort'));

        $this->dataHandler->setTransformer($this->transformer);

        if ($throttle = array_get($this->settings, 'throttle')) {
            $requestProvider->setDefaultThrottle($throttle);
        }

        if ($threshold = array_get($this->settings, 'threshold')) {
            $requestProvider->setDefaultThreshold($threshold);
        }

        if ($download = $requestProvider->getDownload()) {
            $maxResults = $requestProvider->getMaxResults() ?: array_get($this->settings, 'max_results');

            $this->dataHandler->setupDataHandlerContext(false, $maxResults);

            $results = $this->dataHandler->getResults();

            if ($download === 'csv') {
                return $requestProvider->downloadCsv(
                    $results,
                    array_get($this->settings, 'csv_delimiter', ','),
                    array_get($this->settings, 'csv_filename', 'data-grid'),
                    array_get($this->settings, 'csv_parser')
                );
            } elseif ($download === 'json') {
                return $requestProvider->downloadJson(
                    $results,
                    array_get($this->settings, 'json_options'),
                    array_get($this->settings, 'json_filename', 'data-grid')
                );
            } elseif ($download === 'pdf') {
                return $requestProvider->downloadPdf(
                    $results,
                    array_get($this->settings, 'pdf_view', 'cartalyst/data-grid::pdf'),
                    array_get($this->settings, 'pdf_filename', 'data-grid')
                );
            }
        } else {
            $this->dataHandler->setupDataHandlerContext();
        }

        return $this;
    }

    /**
     * Creates a data handler instance from the given data type by
     * matching it to a mapping that's registered with the
     * environment instance.
     *
     * @return \Cartalyst\Datagrid\DataHandlers\HandlerInterface
     * @throws \RuntimeException
     */
    public function createDataHandler()
    {
        foreach ($this->env->getDataHandlerMappings() as $handler => $class) {
            if ($class($this->data) === true) {
                // By calling the setter method we can be sure the
                // resolved class implements the correct interface.
                $instance = new $handler($this);

                $this->setDataHandler($instance);

                return $instance;
            }
        }

        $descriptor = gettype($this->data);

        if (is_object($this->data)) {
            $descriptor = get_class($this->data);
        }

        throw new RuntimeException("Could not determine an appropriate data source for data of type [{$descriptor}].");
    }

    /**
     * Returns the environment used in the data grid.
     *
     * @return \Cartalyst\DataGrid\Environment
     */
    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     * Returns the data used in the data grid.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the columns associated with data grid.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns the data handler.
     *
     * @return \Cartalyst\DataGrid\DataHandlers\HandlerInterface
     */
    public function getDataHandler()
    {
        return $this->dataHandler;
    }

    /**
     * Sets the data handler.
     *
     * @param  \Cartalyst\DataGrid\DataHandlers\HandlerInterface  $dataHandler
     * @return void
     */
    public function setDataHandler(DataHandlerInterface $dataHandler)
    {
        $this->dataHandler = $dataHandler;
    }

    /**
     * Returns the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $handler = $this->dataHandler;

        $requestProvider = $this->env->getRequestProvider();

        return array(
            'total'          => $handler->getTotalCount(),
            'filtered'       => $handler->getFilteredCount(),
            'throttle'       => $requestProvider->getThrottle(),
            'threshold'      => $requestProvider->getThreshold(),
            'page'           => $handler->getPage(),
            'pages'          => $handler->getPagesCount(),
            'previous_page'  => $handler->getPreviousPage(),
            'next_page'      => $handler->getNextPage(),
            'per_page'       => $handler->getPerPage(),
            'sort'           => $handler->getSort(),
            'direction'      => $handler->getDirection(),
            'default_column' => $handler->getDefaultColumn(),
            'results'        => $handler->getResults(),
        );
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the data grid to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
