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

namespace Cartalyst\DataGrid\RequestProviders;

interface ProviderInterface
{
    /**
     * Returns the request object.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();

    /**
     * Returns an array of filters.
     *
     * Filters which have a string for the key, are treated as filters
     * for an attribute whereas others are treated as global filters.
     *
     * @return array
     */
    public function getFilters();

    /**
     * Returns the column by which we sort our datagrid.
     *
     * @return string
     */
    public function getSort();

    /**
     * Returns the direction which we apply sort.
     *
     * @return string
     */
    public function getDirection();

    /**
     * Returns the page which we are on.
     *
     * @return int
     */
    public function getPage();

    /**
     * Returns the method being used.
     *
     * @return int
     */
    public function getMethod();

    /**
     * Returns the threshold (number of results before pagination begins).
     *
     * @return int
     */
    public function getThreshold();

    /**
     * Returns the throttle, which is the maximum results set.
     *
     * @return int
     */
    public function getThrottle();
}
