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

use Exception;
use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Attributes\Relations\EavValues;

trait EntityTrait
{
    /**
     * The name of the relationship to the "value" model.
     *
     * @var string
     */
    protected $valueRelation = 'values';

    /**
     * Holds all the cached attributes.
     *
     * @var array
     */
    protected static $attributesCache = [];

    /**
     * Sets the value relation.
     *
     * @param  string  $valueRelation
     * @return void
     */
    public function setValueRelation($valueRelation)
    {
        $this->valueRelation = $valueRelation;
    }

    /**
     * {@inheritDoc}
     */
    public function getValueRelation()
    {
        return $this->valueRelation;
    }

    /**
     * {@inheritDoc}
     */
    public function values()
    {
        return $this->hasValues('Cartalyst\Attributes\Value', 'entity');
    }

    /**
     * {@inheritDoc}
     */
    public function fill(array $attributes)
    {
        if ($eavAttributes = $this->pluckEavAttributes($attributes, false)) {
            if ($this->fillable) {
                $this->fillable = array_merge($this->fillable, array_keys($eavAttributes));
            }
        }

        return parent::fill($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function save(array $options = [])
    {
        // Firstly, we will grab all EAV attributes out
        // of our attributes array. We will use these after the initial
        // save has taken place
        $eavAttributes = $this->extractEavAttributes();

        $connection = $this->getConnection();

        $result = $connection->transaction(function ($connection) use ($options, $eavAttributes) {
            // We will now take note if the model exists. We approach adding
            // EAV values differently for new models
            $exists = $this->exists;

            $result = parent::save($options);

            if ($result === false) {
                $connection->rollBack();

                return false;
            }

            $eavResult = $this->saveEav($eavAttributes, $exists);

            if ($eavResult === false) {
                $connection->rollBack();

                return false;
            }

            // Restore the EAV attributes back to the original array
            $this->restoreEavAttributes($eavAttributes);

            return $result;
        });

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        if ($this->exists) {
            $connection = $this->getConnection();

            $result = $connection->transaction(function ($connection) {
                $result = parent::delete();

                if ($result === false) {
                    $connection->rollBack();

                    return false;
                }

                $eavResult = $this->deleteEav();

                if ($eavResult === false) {
                    $connection->rollBack();

                    return false;
                }

                return $result;
            });

            return $result;
        }
    }

    /**
     * Saves EAV attributes to the database. Because this will take place
     * after a save, a model always "exists". We take an additional flag
     * to whether the model existed before this first save.
     *
     * If it didn't, attaching values can take place more efficiently with
     * the assumption that there are no attributes for non-existent
     * models.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return bool
     */
    protected function saveEav(array $attributes, $exists)
    {
        if ( ! count($attributes)) {
            return true;
        }

        // If it already exists, it means we have already loaded up our
        // associated EAV "values" and "attributes". We will spin
        // through "values" and update them where possible.
        // If they don't exist, we will create a new "value".
        if ($exists) {
            foreach ($attributes as $key => $value) {
                if ($valueInstance = $this->findValue($key)) {
                    if ($this->updateEavValue($valueInstance, $value) === false) {
                        return false;
                    }
                } elseif ($this->createEavValue($key, $value) === false) {
                    return false;
                }
            }
        }

        // If we didn't exist, we will need to create "value" objects
        // for every attribute value. We will then attach them
        // to our relationship manually, so as to save extra
        // queries when we will know the outcome.
        else {
            foreach ($attributes as $key => $value) {
                $valueInstance = $this->createEavValue($key, $value);

                if ($valueInstance === false) {
                    return false;
                }
            }

            $relation = $this->getValueRelation();

            $this->setRelation($relation, $this->$relation()->get());
        }

        return true;
    }

    /**
     * Creates an EAV "value" object with the given key and value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return \Cartalyst\Attributes\Value|bool
     */
    protected function createEavValue($key, $value)
    {
        if ( ! $value) {
            return;
        }

        // Grab the attribute instance
        $attributeInstance = $this->findAttribute($key);

        // Grab a new value instance and set it's "value"
        $valueInstance = $this->newValueModel();

        $valueInstance->setValueKey($value);

        // Link it with the attribute
        $relation = $attributeInstance->{$attributeInstance->getValueRelation()}();

        $valueInstance->setAttribute($relation->getPlainForeignKey(), $attributeInstance->getKey());

        // Link it with this entity
        $relation = $this->{$this->getValueRelation()}();

        $valueInstance->setAttribute($relation->getPlainForeignKey(), $this->getKey());

        $valueInstance->setAttribute($relation->getPlainMorphType(), $relation->getMorphClass());

        return $valueInstance->save() ? $valueInstance : false;
    }

    /**
     * Updates an EAV "value" object with the given value.
     *
     * @param  \Cartalyst\Attributes\Value  $valueInstance
     * @param  mixed  $value
     * @return \Cartalyst\Attributes\Value|bool
     */
    protected function updateEavValue(Value $valueInstance, $value)
    {
        if ( ! $value) {
            $valueInstance->delete();

            return;
        }

        $valueInstance->setValueKey($value);

        if ($valueInstance->save()) {
            return $valueInstance;
        }
    }

    /**
     * Extracts EAV attributes from the original array of attributes.
     *
     * On new models it will return all EAV attributes. On existing
     * models it will only return dirty attributes as those are
     * the only ones which have changed and which will need
     * to be updated.
     *
     * @return array
     */
    protected function extractEavAttributes()
    {
        if ($this->exists) {
            $dirty = $this->getDirty();

            if (count($dirty) > 0) {
                return $this->pluckEavAttributes($dirty);
            }
        } else {
            return $this->pluckEavAttributes($this->attributes);
        }

        return [];
    }

    /**
     * Takes the given array of attributes, compares them to available
     * attributes and returns the available ones.
     *
     * Also plucks any attributes passed from the original
     * array of the model's attributes.
     *
     * @param  array  $attributes
     * @param  bool  $unset
     * @return array
     */
    protected function pluckEavAttributes(array $attributes, $unset = true)
    {
        $eavAttributes = [];

        foreach ($attributes as $key => $value) {
            $matching = $this->findAttribute($key);

            if ($matching) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                $eavAttributes[$key] = $value;

                if ($unset) {
                    unset($this->attributes[$key]);
                }
            }
        }

        return $eavAttributes;
    }

    /**
     * Restores EAV attributes back into the main array.
     *
     * @param  array  $eavAttributes
     * @return void
     */
    protected function restoreEavAttributes(array $eavAttributes)
    {
        $this->attributes = array_merge($this->attributes, $eavAttributes);
    }

    /**
     * Deletes all "value" data associated with the entity.
     *
     * @return bool
     */
    protected function deleteEav()
    {
        $relation = $this->getValueRelation();

        foreach ($this->$relation as $value) {
            if ( ! $value->delete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @param  bool  $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newQuery($excludeDeleted = true)
    {
        // We will create a nested relation that's dynamically generated so that all
        // attributes and values are eagerly loaded with models in new queries.
        $withRelation = $this->valueRelation.'.'.$this->newValueModel()->getAttributeRelation();

        $this->with = array_merge($this->with, [$withRelation]);

        return parent::newQuery($excludeDeleted);
    }

    /**
     * {@inheritDoc}
     */
    public function hasValues($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related;

        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new EavValues($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * {@inheritDoc}
     */
    public function newValueModel()
    {
        $relation = $this->valueRelation;

        return $this->$relation()->getRelated();
    }

    /**
     * {@inheritDoc}
     */
    public function newAttributeModel()
    {
        return $this->newValueModel()->newAttributeModel();
    }

    /**
     * {@inheritDoc}
     */
    public function findAttribute($key)
    {
        $availableAttributes = static::availableAttributes();

        return array_first($availableAttributes, function ($index, $attribute) use ($key) {
            return ($attribute->getAttributeKey() == $key);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findValue($key)
    {
        if ( ! isset($this->relations[$this->getValueRelation()])) {
            throw new RuntimeException("Implement lazy loading attribute values, here and in the EavValues relationship.");
        }

        return array_first($this->{$this->getValueRelation()}, function ($index, $value) use ($key) {
            return ($value->{$value->getAttributeRelation()}->getAttributeKey() == $key);
        });
    }

    /**
     * Caches all the available attributes, irrespective of model
     * instance (for speed).
     *
     * @return array
     */
    public static function availableAttributes()
    {
        $cacheKey = get_called_class();

        if ( ! isset(static::$attributesCache[$cacheKey])) {
            static::$attributesCache[$cacheKey] = with(new static)->newAttributeModel()->get();
        }

        return static::$attributesCache[$cacheKey];
    }

    /**
     * Clears the cache of the available attributes.
     *
     * @return void
     */
    public static function clearAttributesCache()
    {
        $cacheKey = get_called_class();

        if (isset(static::$attributesCache[$cacheKey])) {
            unset(static::$attributesCache[$cacheKey]);
        }
    }
}
