<?php

/**
 * Part of the Extensions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Extensions
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Extensions;

interface FinderInterface
{
    /**
     * Returns an array of fully qualified extension
     * locations in the registered paths.
     *
     * @return array
     */
    public function findExtensions();

    /**
     * Adds a path to the extensions finder.
     *
     * @param  string  $path
     * @return void
     */
    public function addPath($path);

    /**
     * Returns an array of fully qualified extension
     * locations in the given path.
     *
     * @param  string  $path
     * @return array
     */
    public function findExtensionsInPath($path);
}
