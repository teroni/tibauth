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
use Symfony\Component\HttpFoundation\Request;
use Cartalyst\DataGrid\RequestProviders\Provider;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Cartalyst\DataGrid\RequestProviders\ProviderInterface as RequestProviderInterface;

class Environment
{
    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $requestProvider;

    /**
     * Array of data source mappings for data types, where the key is the
     * applicable class and the value is a closure which determines if
     * the class is applicable for the data type.
     *
     * @var array
     */
    protected $dataHandlerMappings = array();

    /**
     * Constructor.
     *
     * @param  \Cartalyst\DataGrid\RequestProviders\ProviderInterface  $requestProvider
     * @param  array  $dataHandlerMappings
     * @return void
     */
    public function __construct(RequestProviderInterface $requestProvider = null, array $dataHandlerMappings = array())
    {
        $this->requestProvider = $requestProvider ?: new Provider(Request::createFromGlobals());

        $this->dataHandlerMappings = $dataHandlerMappings;
    }

    /**
     * Create a new data grid instance.
     *
     * @param  mixed  $dataHandler
     * @param  array  $columns
     * @param  array  $settings
     * @param  \Closure  $transformer
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function make($dataHandler, array $columns, array $settings = array(), Closure $transformer = null)
    {
        return $this->createDataGrid($dataHandler, $columns, $settings, $transformer)->setupDataGridContext();
    }

    /**
     * Creates a new instance of the data grid.
     *
     * @param  mixed  $dataHandler
     * @param  array  $columns
     * @param  array  $settings
     * @param  \Closure  $transformer
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function createDataGrid($dataHandler, array $columns, array $settings = array(), Closure $transformer = null)
    {
        return new DataGrid($this, $dataHandler, $columns, $settings, $transformer);
    }

    /**
     * Returns the active request instance.
     *
     * @return \Cartalyst\DataGrid\RequestProviders\ProviderInterface
     */
    public function getRequestProvider()
    {
        return $this->requestProvider;
    }

    /**
     * Sets the active request instance.
     *
     * @param  \Cartalyst\DataGrid\RequestProviders\ProviderInterface  $requestProvider
     * @return void
     */
    public function setRequestProvider(RequestProviderInterface $requestProvider)
    {
        $this->requestProvider = $requestProvider;
    }

    /**
     * Returns the data handler mappings.
     *
     * @return array
     */
    public function getDataHandlerMappings()
    {
        return $this->dataHandlerMappings;
    }

    /**
     * Sets data handler mappings.
     *
     * @param  array  $mappings
     * @return void
     */
    public function setDataHandlerMappings(array $mappings)
    {
        $this->dataHandlerMappings = $mappings;
    }

    /**
     * Sets a data handler mapping.
     *
     * @param  string  $class
     * @param  \Closure  $handler
     * @return void
     */
    public function setDataHandlerMapping($class, Closure $handler)
    {
        $this->dataHandlerMappings[$class] = $handler;
    }
}
