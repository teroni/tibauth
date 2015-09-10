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

use InvalidArgumentException;
use Cartalyst\DataGrid\DataGrid;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

class CollectionHandler extends BaseHandler implements HandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function validateData($data)
    {
        // If we have an array, we'll throw it into a collection now
        if (is_array($data)) {
            $data = new Collection($data);
        }

        // We must have a collection by this point. No collection? No go.
        if (! $data instanceof Collection) {
            throw new InvalidArgumentException('Invalid data source passed to collection handler. Must be an array or collection object.');
        }

        // Ensure that our items are arrays as we accept various data types
        $data = $data->map(function ($item) {
            if ($item instanceof Arrayable) {
                $item = $item->toArray();
            }

            return (array) $item;
        });

        return $data->values();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareTotalCount()
    {
        $this->totalCount = $this->data->count();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSelect()
    {
        $columns = $this->dataGrid->getColumns();

        // We'll go ahead and map the columns, only selecting the ones which
        // are required.
        $this->data = $this->data->map(function ($item) use ($columns) {
            $modified = array();

            // If the person is using an alias, we'll
            // be sure to modify the select to work off
            // the alias and not the actual key.
            foreach ($columns as $key => $value) {
                $modifiedItem = array_get($item, is_numeric($key) ? $value : $key);

                $modified[$value] = $modifiedItem;
            }

            return $modified;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function prepareFilters()
    {
        list($columnFilters, $globalFilters) = $this->getFilters();

        if (count($columnFilters) === 0 and count($globalFilters) === 0) {
            return;
        }

        $me = $this;
        $this->data = $this->data->filter(function ($item) use ($me, $columnFilters, $globalFilters) {
            foreach ($columnFilters as $filter) {
                list($column, $operator, $value) = $filter;

                if (! $me->checkColumnFilter($item, $column, $operator, $value)) {
                    return false;
                }
            }

            foreach ($globalFilters as $filter) {
                list($operator, $value) = $filter;

                if (! $me->checkGlobalFilter($item, $operator, $value)) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function prepareFilteredCount()
    {
        $this->filteredCount = $this->data->count();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSort()
    {
        if ($column = $this->calculateSortColumn($this->request->getSort()) ?: $this->sort) {
            $direction = $this->request->getDirection() ?: $this->direction;

            $this->data = $this->data->sort(function ($a, $b) use ($column, $direction) {
                $result = strnatcasecmp(array_get($a, $column), array_get($b, $column));

                $invert = ($direction == 'desc');

                return $result * ($invert ? -1 : 1);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function preparePagination($paginate = true)
    {
        // If our filtered results are zero, let's not set any pagination
        if ($this->filteredCount == 0) {
            return;
        }

        if (! $paginate) {
            return $this->filteredCount;
        }

        $page = $this->request->getPage();

        $method = $this->request->getMethod();

        $threshold = $this->request->getThreshold();

        $throttle = $this->request->getThrottle();

        list($this->pagesCount, $this->perPage) = $this->calculatePagination($this->filteredCount, $method, $threshold, $throttle);

        list($this->page, $this->previousPage, $this->nextPage) = $this->calculatePages($this->filteredCount, $page, $this->perPage);

        // Calculate the offset that's needed to slice our collection
        $offset = ($this->page - 1) * $this->perPage;

        $this->data = $this->data->slice($offset, $this->perPage);
    }

    /**
     * Hydrates the results.
     *
     * @param  int  $maxResults
     * @return void
     */
    public function hydrate($maxResults = null)
    {
        if ($maxResults) {
            $this->results = $this->data->slice(0, $maxResults);
        } else {
            $this->results = $this->data;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRegexFilters()
    {
        return true;
    }

    /**
     * Checks a column filter against the given item in the collection.
     *
     * @param  array $item
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function checkColumnFilter(array $item, $column, $operator, $value)
    {
        $columns = $this->dataGrid->getColumns();

        if (strpos($column, '..') !== false) {
            $cols = explode('..', $column);

            if ($results = array_get($item, head($cols))) {
                foreach ($results as $key => $val) {
                    if (! is_array($val)) {
                        if (strpos($results[last($cols)], $value) !== false) {
                            return true;
                        }
                    }
                }
            }

            return false;
        } elseif (($index = array_search($column, $columns)) !== false) {
            if (! is_numeric($index)) {
                $column = $index;
            }

            $columnValue = $item[$column];

            if (is_array($columnValue)) {
                foreach ($columnValue as $arrayColumnValue) {
                    if (is_array($arrayColumnValue)) {
                        continue;
                    }

                    if (! $this->checkFilterValue($operator, $arrayColumnValue, $value)) {
                        return false;
                    }
                }
            } elseif (! $this->checkFilterValue($operator, $columnValue, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks a filter globally against all columns.
     *
     * @param  array  $item
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function checkGlobalFilter(array $item, $operator, $value)
    {
        foreach ($item as $columnValue) {
            if (is_array($columnValue)) {
                foreach ($columnValue as $arrayColumnValue) {
                    if (is_array($arrayColumnValue)) {
                        continue;
                    }

                    if ($this->checkFilterValue($operator, $arrayColumnValue, $value)) {
                        return true;
                    }
                }
            } elseif ($this->checkFilterValue($operator, $columnValue, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The "guts" of checking whether a filtered value
     * matches a column value, given the operator.
     *
     * @param  string  $operator
     * @param  mixed  $columnValue
     * @param  mixed  $filterValue
     * @return bool
     */
    protected function checkFilterValue($operator, $columnValue, $filterValue)
    {
        $val = true;

        if ($filterValue === 'null') {
            return empty($columnValue);
        }

        switch ($operator) {
            case 'like':
                $val = (stripos($columnValue, $filterValue) !== false);
                break;

            case '<=':
                $val = ($filterValue >= $columnValue);
                break;

            case '>=':
                $val = ($filterValue <= $columnValue);
                break;

            case '<>':
            case '!=':
                $val = ($filterValue != $columnValue);
                break;

            case '=':
                $val = ($filterValue == $columnValue);
                break;

            case '<':
                $val = ($filterValue > $columnValue);
                break;

            case '>':
                $val = ($filterValue < $columnValue);
                break;

            case 'regex':
                $val = preg_match('/'.$filterValue.'/', $columnValue);
                break;
        }

        // No applicable filter
        return $val;
    }
}
