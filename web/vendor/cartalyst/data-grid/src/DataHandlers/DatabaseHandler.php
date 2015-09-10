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

use RuntimeException;
use InvalidArgumentException;
use Cartalyst\Attributes\Value;
use Cartalyst\DataGrid\DataGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\MySqlConnection as MySqlDatabaseConnection;

class DatabaseHandler extends BaseHandler implements HandlerInterface
{
    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = array();

    /**
     * Attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Attributes primary key.
     *
     * @var string
     */
    protected $attributesKey = 'slug';

    /**
     * Eav class.
     *
     * @var string
     */
    protected $eavClass;

    /**
     * {@inheritDoc}
     */
    public function validateData($data)
    {
        $this->eavClass = get_class($data);

        // If the data is an instance of an Eloquent model,
        // we'll grab a new query from it.
        if ($data instanceof Model) {
            $this->appends = array_keys($data->attributesToArray());

            if (method_exists($data, 'availableAttributes')) {
                $attributes = $data->availableAttributes()->lists($this->attributesKey);

                if (method_exists($attributes, 'toArray')) {
                    $this->attributes = $attributes->toArray();
                } else {
                    $this->attributes = $attributes;
                }
            }

            $data = $data->newQuery();
        } elseif ($data instanceof EloquentQueryBuilder) {
            $model = $data->getModel();

            $this->appends = array_keys($model->attributesToArray());

            if (method_exists($model, 'availableAttributes')) {
                $attributes = $model->availableAttributes()->lists($this->attributesKey);

                if (method_exists($attributes, 'toArray')) {
                    $this->attributes = $attributes->toArray();
                } else {
                    $this->attributes = $attributes;
                }
            }
        }

        // We accept different data types for our data grid,
        // let's just check now that
        if (! $data instanceof QueryBuilder and
             ! $data instanceof EloquentQueryBuilder and
             ! $data instanceof HasMany and
             ! $data instanceof BelongsToMany) {
            throw new InvalidArgumentException('Invalid data source passed to database handler. Must be an Eloquent model / query / valid relationship, or a databse query.');
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareTotalCount()
    {
        $this->totalCount = $this->prepareCount();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSelect()
    {
        // Fallback array to select
        $toSelect = array();

        // Loop through columns and inspect whether they are an alias or not.
        //
        // If the key is not numeric, it is the real column name and the
        // value is the alias. Otherwise, there is no alias and we're
        // dealing directly with the column name. Aliases are used
        // quite often for joined tables.
        foreach ($this->dataGrid->getColumns() as $key => $value) {
            if (! array_get($this->appends, $value) && array_search($value, $this->attributes) === false) {
                $toSelect[] = is_numeric($key) ? $value : "{$key} as {$value}";
            }
        }

        $this->data->addSelect($toSelect);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareFilters()
    {
        $me = $this;

        list($columnFilters, $globalFilters) = $this->getFilters();

        foreach ($columnFilters as $filter) {
            list($column, $operator, $value) = $filter;

            $this->applyFilter($this->data, $column, $operator, $value);
        }

        foreach ($globalFilters as $filter) {
            list($operator, $value) = $filter;

            $this->data->whereNested(function ($data) use ($me, $operator, $value) {
                $me->globalFilter($data, $operator, $value);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepareFilteredCount()
    {
        $this->filteredCount = $this->prepareCount();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSort()
    {
        $column = $this->calculateSortColumn($this->request->getSort()) ?: $this->sort;

        if ($column) {
            $direction = $this->request->getDirection() ?: $this->direction;

            $data = $this->data;

            if ($data instanceof HasMany or $data instanceof BelongsToMany) {
                $data = $data->getQuery();
            }

            if ($data instanceof EloquentQueryBuilder) {
                $data = $data->getQuery();
            }

            // We are going to prepend our sort order to the data
            // as SQL allows for multiple sort. By appending it,
            // a predefined sort may override ours.
            if (is_array($data->orders)) {
                array_unshift($data->orders, compact('column', 'direction'));
            }

            // If no orders have been defined, the orders property
            // is set to null. At this point, we cannot unshift a
            // sort order to the front, so we will use the API.
            else {
                $data->orderBy($column, $direction);
            }
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

        $this->data->forPage($this->page, $this->perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($maxResults = null)
    {
        if ($maxResults) {
            $this->data->limit($maxResults);
        }

        $this->results = $this->data->get();
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRegexFilters()
    {
        $regex = false;

        switch ($connection = $this->getConnection()) {
            case $connection instanceof MySqlDatabaseConnection:
                $regex = true;
                break;
        }

        return $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function calculateSortColumn($column = null)
    {
        if (! $column) {
            return;
        }

        $index = array_search($column, $this->dataGrid->getColumns());

        $key = $index !== false ? $index : false;

        // If the sort column doesn't exist, something has gone wrong
        if ($key === false) {
            throw new RuntimeException("Sort column [{$column}] does not exist in data.");
        }

        // If our column is an alias, we'll use the actual
        // value instead of the alias for sorting.
        if (! is_numeric($key) && ! is_bool($key)) {
            $column = $key;
        }

        return $column;
    }

    /**
     * Applies a global filter across all registered columns. The
     * filter is applied in a "or where" fashion, where
     * the value can be matched across any column.
     *
     * @param  \Illuminate\Database\Query\Builder  $nestedQuery
     * @param  string  $operator
     * @param  string  $value
     * @return void
     */
    public function globalFilter(QueryBuilder $nestedQuery, $operator, $value)
    {
        foreach ($this->dataGrid->getColumns() as $key => $_value) {
            if (is_numeric($key)) {
                $key = $_value;
            }

            $this->applyFilter($nestedQuery, $key, $operator, $value, 'or');
        }
    }

    /**
     * Returns the connection associated with the handler's data set.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function getConnection()
    {
        $data = $this->data;

        if ($data instanceof EloquentQueryBuilder) {
            $data = $data->getQuery();
        }

        return $data->getConnection();
    }

    /**
     * Applies a filter to the given query.
     *
     * @param  mixed  $query
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed  $value
     * @param  string  $method
     * @return void
     */
    protected function applyFilter($query, $column, $operator, $value, $method = 'and')
    {
        $method = ($method === 'and') ? 'where' : 'orWhere';

        switch ($operator) {
            case 'like':
                $value = "%{$value}%";
                break;

            case 'regex':

                if ($this->supportsRegexFilters()) {
                    $method .= 'Raw';
                }

                if ($this->getConnection() instanceof MySqlDatabaseConnection) {
                    $query->{$method}("{$column} {$operator} ?", array($value));
                }

                return;
        }

        if (strpos($column, '..') !== false) {
            $cols = explode('..', $column);

            $query->whereHas(head($cols), function ($q) use ($cols, $operator, $value) {
                $q->where(last($cols), $operator, $value);
            });
        } elseif (array_search($column, $this->attributes) !== false) {
            $valueModel = new Value;

            $matches = $valueModel->newQuery()
                ->where('entity_type', $this->eavClass)
                ->{$method}('value', $operator, $value)
                ->get();

            $key = $query->getModel()->getKeyName();

            if (! $matches->toArray()) {
                $query->where($key, null);
            }

            foreach ($matches as $match) {
                $query->{$method}($key, $operator, $match->entity_id);
            }
        } else {
            if ($value === '%null%') {
                $query->whereNull($column);
            } elseif ($value === '%not_null%') {
                $query->whereNotNull($column);
            } else {
                $query->{$method}($column, $operator, $value);
            }
        }
    }

    /**
     * Counts data records.
     * Accounts for the bug #4306 on laravel/framework
     * @return [type] [description]
     */
    protected function prepareCount()
    {
        $data = $this->data;

        if (method_exists($data, 'getQuery')) {
            $data = $data->getQuery();
        }

        if (empty($data->groups)) {
            return $data->count();
        }

        return count($data->get());
    }
}
