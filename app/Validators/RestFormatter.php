<?php

namespace App\Validators;

class RestFormatter
{
    /**
     *
     * @var \Illuminate\Validation\Validator 
     */
    protected $validator;

    /**
     * 
     * @param \Illuminate\Validation\Validator $validator
     */
    public function __construct($validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * 
     * @return array
     */
    public function errors()
    {
        $errors = $this->validator->messages()->toArray();
        $results = [];
        foreach ($errors as $field => $messages) {
            $results[] = ['field' => $field, 'messages' => $messages];
        }
        
        return $results;
    }
}