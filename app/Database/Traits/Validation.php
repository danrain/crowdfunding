<?php

namespace App\Database\Traits;

use Validator;
use Exception;

trait Validation
{    
    /**
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator = null;


    /**
     * Some time we need some fields to validate (like confirmed attributes)
     * But we don't need storage them to db. They are will be removed before saving
     * @var array
     * protected $onlyValidationFields = [];
     */
    
    /**
     * 
     * @throws Exception
     */
    public static function bootValidation()
    {
        if ( !property_exists(get_called_class(), 'rules')) {
            throw new Exception(sprintf('You must define a $rules property in %s to use the Validation trait.', get_called_class()));
        }
        
        static::saving(function ($model) {
            $model->removeOnlyValidationFields();
        });
    }
    
    /**
     * 
     * @return type
     */
    public function removeOnlyValidationFields()
    {
        if ( !empty($this->onlyValidationFields) ) {
            $attributes = $this->getAttributes();
            $removeFields = $this->onlyValidationFields;
            $cleanAttributes = array_diff_key($attributes, array_flip($removeFields));
            return $this->attributes = $cleanAttributes;
        }
    }
    
    /**
     * Make validator 
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     * @return Validator
     */
    public function validator($data = [], $rules = [], $customMessages = [])
    {
        if ( ! $this->validator) {
            $this->validator = Validator::make($data, $rules, $customMessages);
        }
        
        return $this->validator;
    }
    
    /**
     * 
     * @param array|null $rules
     * @param array|null $customMessages
     * @return boolean
     */
    public function validate($rules = null, $customMessages = null)
    {   
        if ( null === $rules) {
            $rules = $this->rules;
        }
        
        $rules = $this->processValidationRules($rules);
        $valid = true;
        if (!empty($rules)) {
            if (property_exists($this, 'customMessages') && is_null($customMessages) ) {
                $customMessages = $this->customMessages;
            }

            if ( is_null($customMessages) ) {
                $customMessages = [];
            }
            
            $data = $this->getAttributes();
            $validator = $this->validator($data, $rules, $customMessages);
            return $validator->passes();
        }
        
        return $valid;
    }
    
    /**
     * 
     * @param array $rules
     * @return array
     */
    protected function processValidationRules($rules)
    {
        foreach ($rules as $field => $fieldRules) {
            if (is_string($fieldRules) && trim($fieldRules) == '') {
                unset($rules[$field]);
                continue;
            }
            
            if ( !is_array($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }
            
            foreach ($fieldRules as $key => $rule) {
                if (starts_with($rule, 'unique') && $this->exists) {
                    $fieldRules[$key] = 'unique:' . $this->getTable() . ',' . $field .',' . $this->getKey();
                }
                
                // Add model table if empty
                if ( 'unique' === $rule && !$this->exists) {
                    $fieldRules[$key] = $rule . ':' . $this->getTable();
                }
                
                /*
                 * Look for required:create and required:update rules
                 */
                else if (starts_with($rule, 'required:create') && $this->exists) {
                    unset($fieldRules[$key]);
                }
                else if (starts_with($rule, 'required:update') && !$this->exists) {
                    unset($fieldRules[$key]);
                } else if ($this->exists && starts_with($rule, 'confirmed') && !$this->isDirty($field)) {
                    unset($fieldRules[$key]);
                }
            }
            
            $rules[$field] = $fieldRules;
        }
        
        return $rules;
    }


    /**
     * Get validation error message collection for the Model
     * 
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->validator->messages();
    }
}