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

class Attribute extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'attributes';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'slug',
    ];

    /**
     * The column name which contains the "key" component in EAV.
     *
     * @var string
     */
    protected $attributeKey = 'slug';

    /**
     * The name of the relationship to the "value" model.
     *
     * @var string
     */
    protected $valueRelation = 'values';

    /**
     * EAV attribute values relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany('Cartalyst\Attributes\Value');
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $relation = $this->valueRelation;

        $this->$relation()->delete();

        return parent::delete();
    }

    /**
     * Return the value of the model's "attribute" key.
     *
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->getAttribute($this->getAttributeKeyName());
    }

    /**
     * Return the "attribute" key for the model.
     *
     * @return string
     */
    public function getAttributeKeyName()
    {
        return $this->attributeKey;
    }

    /**
     * Returns the "value" relation name.
     *
     * @return string
     */
    public function getValueRelation()
    {
        return $this->valueRelation;
    }

    /**
     * Returns a new instance of a "value" model.
     *
     * @return \Cartalyst\Attributes\ValuableInterface
     */
    public function newValueModel()
    {
        $relation = $this->valueRelation;

        return $this->$relation()->getRelated();
    }

    /**
     * Returns a new instance of an "entity" model.
     *
     * @return \Cartalyst\Attributes\ValuableInterface
     */
    public function newEntityModel()
    {
        return $this->newValueModel()->newEntityModel();
    }

    /**
     * {@inheritDoc}
     */
    protected function finishSave(array $options)
    {
        parent::finishSave($options);

        EntityTrait::clearAttributesCache();
    }

    /**
     * {@inheritDoc}
     */
    protected function performDeleteOnModel()
    {
        parent::performDeleteOnModel();

        EntityTrait::clearAttributesCache();
    }
}
