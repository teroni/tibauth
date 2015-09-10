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

use Illuminate\Validation\Factory as Validator;

class SectionValidate
{
    /**
     * The Validator instance.
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Validation\Factory  $validator
     * @return void
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates the given section fieldset fields rules against the given data.
     *
     * @param  \Cartalyst\Settings\Section  $section
     * @param  array  $data
     * @return \Illuminate\Support\MessageBag
     */
    public function validate(Section $section, array $data)
    {
        $validator = $this->validator->make($data, $this->getRules($section));

        return $validator->errors();
    }

    /**
     * Returns all the section fieldset fields rules.
     *
     * @param  \Cartalyst\Settings\Section  $section
     * @return array
     */
    protected function getRules(Section $section)
    {
        $rules = [];

        foreach ($section->all() as $fieldset) {
            foreach ($fieldset->all() as $field) {
                if ($_rules = $field->rules) {
                    $rules[$field->id] = $_rules;
                }
            }
        }

        return $rules;
    }
}
