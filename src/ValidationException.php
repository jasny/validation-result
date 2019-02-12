<?php

namespace Jasny;

use UnexpectedValueException;

/**
 * Validation exception
 */
class ValidationException extends \RuntimeException
{
    /**
     * @var ValidationResult
     */
    protected $validationResult;

    /**
     * ValidationException constructor.
     *
     * @param ValidationResult $validation
     * @throws UnexpectedValueException
     */
    public function __construct(ValidationResult $validation)
    {
        if (!$validation->failed()) {
            throw new UnexpectedValueException('Validation didn\'t fail, no exception should be thrown');
        }

        parent::__construct("Validation failed;\n * " . join("\n * ", $validation->getErrors()));

        $this->validationResult = $validation;
    }

    /**
     * Get the validation result with the validation errors.
     *
     * @return ValidationResult
     */
    public function getValidationResult()
    {
        return $this->validationResult;
    }

    /**
     * Get the (first) validation error
     *
     * @return string
     */
    public function getError()
    {
        return $this->validationResult->getError();
    }

    /**
     * Get the validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->validationResult->getErrors();
    }


    /**
     * Factory method for failed validation
     *
     * @param string $message
     * @param mixed  ...$args  Arguments to insert into the message
     * @return static
     */
    public static function error($message, ...$args)
    {
        $error = ValidationResult::error($message, ...$args);

        return new static($error);
    }
}
