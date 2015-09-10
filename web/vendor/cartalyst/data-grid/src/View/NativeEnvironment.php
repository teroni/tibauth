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

namespace Cartalyst\DataGrid\View;

class NativeEnvironment
{
    /**
     * Renders the view.
     *
     * @param  string  $view
     * @param  array  $data
     * @return string
     */
    public function make($view, $data)
    {
        extract($data);

        $src = file_get_contents($view);
        $tmp = tempnam("/tmp", "pdf");

        file_put_contents($tmp, $src);

        ob_start();

        include $tmp;

        return ob_get_clean();
    }
}
