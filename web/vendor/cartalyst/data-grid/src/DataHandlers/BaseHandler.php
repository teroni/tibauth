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

namespace Cartalyst\DataGrid\DataHandlers;

use Closure;
use RuntimeException;
use InvalidArgumentException;
use Cartalyst\DataGrid\DataGrid;
use Illuminate\Support\Collection;

abstract class BaseHandler implements HandlerInterface
{
    /**
     * The Data Grid instance.
     *
     * @var \Cartalyst\DataGrid\DataGrid
     */
    protected $dataGrid;

    /**
     * The data we use.
     *
     * @var mixed
     */
    protected $data;

    /**
     * The request provider.
     *
     * @var \Cartalyst\DataGrid\RequestProviders\ProviderInterface
     */
    protected $request;

    /**
     * Cached total (unfiltered) count of results.
     *
     * @var int
     */
    protected $totalCount = 0;

    /**
     * Cached filtered count of results.
     *
     * @var int
     */
    protected $filteredCount = 0;

    /**
     * Cached current page.
     *
     * @var int
     */
    protected $page = 1;

    /**
     * Cached number of pages.
     *
     * @var int
     */
    protected $pagesCount = 1;

    /**
     * Cached previous page.
     *
     * @var int|null
     */
    protected $previousPage;

    /**
     * Cached next page.
     *
     * @var int|null
     */
    protected $nextPage;

    /**
     * Cached number of results per page.
     *
     * @var int|null
     */
    protected $perPage;

    /**
     * Cached sort.
     *
     * @var string
     */
    protected $sort;

    /**
     * Cached direction.
     *
     * @var string
     */
    protected $direction;

    /**
     * Cached default sort column.
     *
     * @var string
     */
    protected $defaultColumn;

    /**
     * Transformer closure.
     *
     * @var \Closure
     */
    protected $transformer = null;

    /**
     * Cached results.
     *
     * @var array
     */
    protected $results = array();

    /**
     * {@inheritDoc}
     */
    public function __construct(DataGrid $dataGrid)
    {
        $this->dataGrid = $dataGrid;

        $this->data = $this->validateData($dataGrid->getData());

        $this->request = $dataGrid->getEnvironment()->getRequestProvider();
    }

    /**
     * Returns the data source from the handler.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilteredCount()
    {
        return $this->filteredCount;
    }

    /**
     * {@inheritDoc}
     */
    public function setFilteredCount($filteredCount)
    {
        $this->filteredCount = $filteredCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * {@inheritDoc}
     */
    public function getPagesCount()
    {
        return $this->pagesCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousPage()
    {
        return $this->previousPage;
    }

    /**
     * {@inheritDoc}
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * {@inheritDoc}
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * {@inheritDoc}
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * {@inheritDoc}
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * {@inheritDoc}
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * Returns the default column.
     *
     * @return void
     */
    public function getDefaultColumn()
    {
        return $this->defaultColumn;
    }

    /**
     * Sets the default column.
     *
     * @param  string  $defaultColumn
     * @return void
     */
    public function setDefaultColumn($defaultColumn)
    {
        $this->defaultColumn = $defaultColumn;
    }

    /**
     * Returns transformer closure.
     *
     * @return \Closure
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Set transformer closure.
     *
     * @param  \Closure  $transformer
     * @return void
     */
    public function setTransformer(Closure $transformer = null)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritDoc}
     */
    public function getResults()
    {
        $results = $this->results;

        if ($transformer = $this->transformer) {
            if ($results instanceof Collection) {
                $results->transform($transformer);
            }
        }

        if ($results instanceof Collection) {
            $results = $results->toArray();
        }

        // Now we return our results in an array form
        $results = array_map(function ($result) {
            return (array) $result;
        }, (array) $results);

        return $results;
    }

    /**
     * Validate the data store.
     *
     * @param  mixed  $data
     * @return mixed  $data
     * @throws \InvalidArgumentException
     */
    abstract public function validateData($data);

    /**
     * Prepares the total count of results before we apply filters.
     *
     * @return void
     */
    abstract public function prepareTotalCount();

    /**
     * Prepares the "select" component of the statement
     * based on the columns array provided.
     *
     * @return void
     */
    abstract public function prepareSelect();

    /**
     * Loops through all filters provided in the data
     * and manipulates the data.
     *
     * @return void
     */
    abstract public function prepareFilters();

    /**
     * Sets up the filtered results count (before pagination).
     *
     * @return void
     */
    abstract public function prepareFilteredCount();

    /**
     * Sets up the sorting for the data.
     *
     * @return void
     */
    abstract public function prepareSort();

    /**
     * Sets up pagination for the data grid. Our pagination
     * is special, see calculatePerPage() for more information.
     *
     * @param  bool  $paginate
     * @return void
     */
    abstract public function preparePagination($paginate = true);

    /**
     * Hydrates the results.
     *
     * @return void
     */
    abstract public function hydrate($maxResults);

    /**
     * Flag for whether the handler supports complex filters.
     *
     * @return void
     */
    abstract public function supportsRegexFilters();

    /**
     * {@inheritDoc}
     */
    public function setupDataHandlerContext($paginate = true, $maxResults = null)
    {
        // Before we apply any filters, we need to setup the total count.
        $this->prepareTotalCount();

        // Apply all the filters requested
        $this->prepareFilters();

        // Setup filtered count
        $this->prepareFilteredCount();

        // Setup the requested sorting
        $this->prepareSort();

        // And we'll setup pagination, pagination
        // is rather unique in the data grid.
        $this->preparePagination($paginate);

        // We'll now setup what columns we will select
        $this->prepareSelect();

        // Hydrate our results
        $this->hydrate($maxResults);

        return $this;
    }

    /**
     * Calculates sort from the request.
     *
     * @param  string  $column
     * @return array
     * @throws \RuntimeException
     */
    public function calculateSortColumn($column = null)
    {
        if (! $column) {
            return;
        }

        $key = array_search($column, $this->dataGrid->getColumns());

        // If the sort column doesn't exist, something has
        // gone wrong. Failing silently could confuse people.
        if ($key === false) {
            throw new RuntimeException("Sort column [{$column}] does not exist in data.");
        }

        // If our column is an alias, we'll use the actual value instead
        // of the alias for sorting.
        if (! is_numeric($key)) {
            $column = $key;
        }

        return $column;
    }

    /**
     * Calculates the pagination for the data grid. If the
     * result is below the threshold we return all results.
     *
     * We return an array with two values, the first one being the number
     * of pages, the second one being the number of results per page.
     *
     * @param  int  $resultsCount
     * @param  string  $method
     * @param  int  $threshold
     * @param  int  $throttle
     * @return array
     * @throws \InvalidArgumentException
     */
    public function calculatePagination($resultsCount, $method, $threshold, $throttle)
    {
        if ($throttle < 1) {
            throw new InvalidArgumentException("Invalid throttle of [{$throttle}], must be [1] or more.");
        }

        // If our results count is less than the threshold,
        // we're always returning one page with all of the items
        // on it. This will effectively remove pagination.
        if ($resultsCount < $threshold) {
            return array(1, $resultsCount);
        }

        $perPage = $method === 'group' ? ceil($resultsCount / $throttle) : $throttle;

        // To work out the number of pages, we'll just divide the
        // results count by the number of results per page.
        $pagesCount = ceil($resultsCount / $perPage);

        return array((int) $pagesCount, (int) $perPage);
    }

    /**
     * Calculates the page, common logic used in multiple handlers.
     *
     * Returns the current page, the previous page and the next page.
     *
     * @param  int  $resultsCount
     * @param  int  $page
     * @param  int  $perPage
     * @return array
     */
    public function calculatePages($resultsCount, $page, $perPage)
    {
        $previousPage = null;

        $nextPage = null;

        // Now we will generate the previous and next page links
        if ($page > 1) {
            if ((($page - 1) * $perPage) <= $resultsCount) {
                $previousPage = $page - 1;
            } else {
                $previousPage = ceil($resultsCount / $perPage);
            }
        }

        if (($page * $perPage) < $resultsCount) {
            $nextPage = $page + 1;
        }

        return array($page, $previousPage, $nextPage);
    }

    /**
     * Grabs the filters from the current request environment, adapts and
     * parses complex filters and presents them in a useful way for
     * processing by the handler.
     *
     * @return array
     */
    public function getFilters()
    {
        $columnFilters = array();

        $globalFilters = array();

        foreach ($this->request->getFilters() as $filter) {
            // If the filter is an array where the key matches one of our
            // columns, we're filtering that column.
            if (is_array($filter)) {
                $filterValue = reset($filter);

                $filterColumn = key($filter);

                if (! $this->data instanceof Collection) {
                    if (($index = array_search($filterColumn, $this->dataGrid->getColumns())) !== false) {
                        if (! is_numeric($index)) {
                            $filterColumn = $index;
                        }
                    }
                }

                foreach ($this->extractFilterFeatures($filterValue) as $feature) {
                    list($featureOperator, $featureValue) = $feature;

                    $columnFilters[] = array($filterColumn, $featureOperator, $featureValue);
                }
            }

            // Otherwise if a string was provided, the filter is an
            // "or where" filter across all columns.
            elseif (is_string($filter)) {
                foreach ($this->extractFilterFeatures($filter) as $feature) {
                    list($featureOperator, $featureValue) = $feature;

                    $globalFilters[] = array($featureOperator, $featureValue);
                }
            }
        }

        return array($columnFilters, $globalFilters);
    }

    /**
     * Extracts filter features from the given value, depending on
     * the existence of complex filters.
     *
     * @param  string  $filterValue
     * @return array
     */
    public function extractFilterFeatures($filterValue)
    {
        // Conditional check for whether the collection supports
        // complex filters or not.
        if ($this->supportsRegexFilters() and preg_match('/^\/(.*)\/$/', $filterValue, $matches)) {
            return array(array('regex', $matches[1]));
        }

        // Operator
        if (preg_match('/^\|(.*)\|$/', $filterValue, $matches)) {
            $clauses = explode('|', $matches[1]);

            $operators = array('<=', '>=', '<>', '!=', '=', '<', '>');

            $features = array();

            foreach ($clauses as $clause) {
                foreach ($operators as $operator) {
                    if (strpos($clause, $operator) === 0) {
                        $features[] = array($operator, substr($clause, strlen($operator)));

                        break;
                    }
                }
            }

            return $features;
        }

        return array(array('like', $filterValue));
    }
}
