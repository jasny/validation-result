<?php

namespace Jasny;

/**
 * Validation result
 */
class ValidationResult
{
    /**
     * Callback for translating the error message
     * @var callback
     */
    public static $translate;
    
    
    /**
     * @var array
     */
    protected $errors = [];

    
    /**
     * Add an error
     * 
     * @param string $message
     * @param mixed  ...       Arguments to insert into the message
     */
    public function addError($message)
    {
        if (isset(static::$translate)) $message = call_user_func(static::$translate, $message);
        
        if (func_num_args() > 1) {
            $args = [0 => $message] + func_get_args();
            $message = call_user_func_array('sprintf', $args);
        }
        
        $this->errors[] = $message;
    }
    
    /**
     * Add errors from a validation object
     * 
     * @param ValidationResult $validation
     * @param string           $prefix
     */
    public function add(ValidationResult $validation, $prefix = '')
    {
        foreach ($validation->getErrors() as $err) {
            $this->errors[] = $prefix . $err; 
        }
    }
    
    
    /**
     * Check if there are no validation errors
     * 
     * @return boolean
     */
    public function succeeded()
    {
        return empty($this->errors);
    }
    
    /**
     * Alias of succeeded()
     * 
     * @return boolean
     */
    final public function isSuccess()
    {
        return $this->succeeded();
    }
    
    /**
     * Check if there are validation errors
     * 
     * @return boolean
     */
    public function failed()
    {
        return !empty($this->errors);
    }
    
    
    /**
     * Get the (first) validation error
     * 
     * @return string|null
     */
    public function getError()
    {
        if (count($this->errors) > 1) trigger_error("There are multiple errors", E_USER_WARNING);
        
        return reset($this->errors) ?: null;
    }
    
    /**
     * Get the validation errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    

    /**
     * Factory method for successfull validation
     * 
     * @return static
     */
    public static function success()
    {
        return new static();
    }
    
    /**
     * Factory method for failed validation
     * 
     * @param string $message
     * @return static
     */
    public static function error($message)
    {
        $validation = new static();
        $validation->addError($message);
        
        return $validation;
    }
}
