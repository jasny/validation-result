<?php

namespace Jasny;

use UnexpectedValueException;

/**
 * Validation exception
 */
class ValidationException extends \RuntimeException
{
    protected ValidationResult $validationResult;

    /**
     * ValidationException constructor.
     *
     * @throws UnexpectedValueException
     */
    public function __construct(ValidationResult $validation)
    {
        if (!$validation->failed()) {
            throw new UnexpectedValueException("Validation didn't fail, no exception should be thrown");
        }

        parent::__construct("Validation failed;\n * " . join("\n * ", $validation->getErrors()));

        $this->validationResult = $validation;
    }

    /**
     * Get the validation result with the validation errors.
     */
    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    /**
     * Get the (first) validation error
     */
    public function getError(): string
    {
        return $this->validationResult->getError();
    }

    /**
     * Get the validation errors
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->validationResult->getErrors();
    }


    /**
     * Factory method for failed validation
     */
    public static function error(string $message, mixed ...$args): static
    {
        $error = ValidationResult::error($message, ...$args);

        return new static($error);
    }
}
