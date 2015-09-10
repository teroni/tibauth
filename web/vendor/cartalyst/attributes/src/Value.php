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

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'attribute_values';

    /**
     * The column name which contains the "value" component in EAV.
     *
     * @var mixed
     */
    protected $valueKey = 'value';

    /**
     * The name of the relationship to the "attribute" model.
     *
     * @var string
     */
    protected $attributeRelation = 'attribute';

    /**
     * The name of the relationship to the "entity" model.
     *
     * @var string
     */
    protected $entityRelation = 'entity';

    /**
     * EAV attribute relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('Cartalyst\Attributes\Attribute');
    }

    /**
     * EAV entity relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }

    /**
     * Return the value of the model's "value" key.
     *
     * @return mixed
     */
    public function getValueKey()
    {
        $valueKey = $this->getAttribute($this->getValueKeyName());

        return is_array(json_decode($valueKey, true)) ? json_decode($valueKey, true) : $valueKey;
    }

    /**
     * Set the value of the model's "value" key.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueKey($value)
    {
        $this->setAttribute($this->getValueKeyName(), $value);
    }

    /**
     * Return the "value" key for the model.
     *
     * @return string
     */
    public function getValueKeyName()
    {
        return $this->valueKey;
    }

    /**
     * Get the "attribute" relation name.
     *
     * @return string
     */
    public function getAttributeRelation()
    {
        return $this->attributeRelation;
    }

    /**
     * Get the "entity" relation name.
     *
     * @return string
     */
    public function getEntityRelation()
    {
        return $this->entityRelation;
    }

    /**
     * Returns a new instance of an "attribute" model.
     *
     * @return \Cartalyst\Attributes\Value
     */
    public function newAttributeModel()
    {
        $relation = $this->attributeRelation;

        return $this->$relation()->getRelated();
    }

    /**
     * Returns a new instance of an "entity" model.
     *
     * @return \Cartalyst\Attributes\Value
     */
    public function newEntityModel()
    {
        $relation = $this->entityRelation;

        return $this->$relation()->getRelated();
    }
}
