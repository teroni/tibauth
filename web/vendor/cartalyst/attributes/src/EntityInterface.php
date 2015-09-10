<?php

/**
 * Part of the Attributes package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Attributes
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Attributes;

interface EntityInterface
{
    /**
     * EAV entity values relationship.
     *
     * @return \Cartalyst\Attributes\Value
     */
    public function values();

    /**
     * Define an EAV value which belongs has values.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function hasValues($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Get the "value" relation name.
     *
     * @return string
     */
    public function getValueRelation();

    /**
     * Returns a new instance of a "value" model.
     *
     * @return \Cartalyst\Attributes\Value
     */
    public function newValueModel();

    /**
     * Returns a new instance of an "attribute" model.
     *
     * @return \Cartalyst\Attributes\Value
     */
    public function newAttributeModel();

    /**
     * Find a matching attribute instance for the given key.
     *
     * @param  string  $key
     * @return \Cartalyst\Attributes\Attribute
     */
    public function findAttribute($key);

    /**
     * Find's an attribute "value" object with the given attribute "key".
     *
     * @param  string  $key
     * @return \Cartalyst\Attributes\Value
     */
    public function findValue($key);
}
