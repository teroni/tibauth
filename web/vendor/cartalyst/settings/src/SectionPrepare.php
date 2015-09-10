<?php

/**
 * Part of the Settings package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Settings
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Settings;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Config\Repository as Config;

class SectionPrepare
{
    /**
     * The Config instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Config $config, Request $request)
    {
        $this->config = $config;

        $this->request = $request;
    }

    /**
     * Prepares the given section fieldset fields.
     *
     * @param  \Cartalyst\Settings\Section  $section
     * @return array
     */
    public function prepare(Section $section)
    {
        $data = $section->all();

        foreach ($data as $fieldset) {
            foreach ($fieldset->all() as $field) {
                if ( ! $config = $field->config) {
                    throw new InvalidArgumentException("Field [{$field->id}] from section [$section->id] is missing the \"config\" attribute!");
                }

                $field->value = $this->request->old($field->id, $this->config->get($config));
            }
        }

        return $data;
    }
}
