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

use Cartalyst\DataGrid\DataGrid;

interface HandlerInterface
{
    /**
     * Constructor.
     *
     * @param  \Cartalyst\DataGrid\DataGrid  $dataGrid
     * @return void
     */
    public function __construct(DataGrid $dataGrid);

    /**
     * Sets up the data source context.
     *
     * @param  bool  $paginate
     * @return \Cartalyst\DataGrid\Handler\HandlerInterface
     */
    public function setupDataHandlerContext($paginate = true);

    /**
     * Returns the total (unfiltered) count of results.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Returns the filtered count of results.
     *
     * @return int
     */
    public function getFilteredCount();

    /**
     * Sets the filtered count property.
     *
     * @param  int  $filteredCount
     * @return void
     */
    public function setFilteredCount($filteredCount);

    /**
     * Returns the current page we are on.
     *
     * @return int
     */
    public function getPage();

    /**
     * Returns the number of pages.
     *
     * @return int
     */
    public function getPagesCount();

    /**
     * Returns the previous page.
     *
     * @return int|null
     */
    public function getPreviousPage();

    /**
     * Returns the next page.
     *
     * @return int|null
     */
    public function getNextPage();

    /**
     * Returns the results.
     *
     * @return int
     */
    public function getResults();

    /**
     * Returns the sort.
     *
     * @return string
     */
    public function getSort();

    /**
     * Sets the sort.
     *
     * @param  string  $sort
     * @return void
     */
    public function setSort($sort);

    /**
     * Returns the direction.
     *
     * @return string
     */
    public function getDirection();

    /**
     * Sets the direction.
     *
     * @param  string  $direction
     * @return void
     */
    public function setDirection($direction);
}
