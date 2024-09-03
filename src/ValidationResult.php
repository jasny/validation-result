<?php

namespace Jasny;

/**
 * Validation result
 */
class ValidationResult
{
    /**
     * Callback for translating the error message
     * @var callable|null
     */
    public static $translate;


    /**
     * @var string[]
     */
    protected array $errors = [];


    /**
     * Translate a message
     */
    public function translate(string $message): string
    {
        return isset(static::$translate) ? (static::$translate)($message) : $message;
    }

    /**
     * Add an error
     *
     * @param string $message
     * @param mixed  ...$args  Arguments to insert into the message
     */
    public function addError(string $message, mixed ...$args): void
    {
        $message = $this->translate($message);
        if (!empty($args)) {
            $message = vsprintf($message, $args);
        }

        $this->errors[] = $message;
    }

    /**
     * Add errors from a validation object
     */
    public function add(ValidationResult $validation, ?string $prefix = null): void
    {
        $prefix = $prefix !== null ? $this->translate($prefix) : null;

        foreach ($validation->getErrors() as $err) {
            $this->errors[] = ($prefix ? trim($prefix) . ' ' : '') . $err;
        }
    }


    /**
     * Check if there are no validation errors.
     */
    public function succeeded(): bool
    {
        return empty($this->errors);
    }

    /**
     * Alias of succeeded()
     */
    final public function isSuccess(): bool
    {
        return $this->succeeded();
    }

    /**
     * Check if there are validation errors
     */
    public function failed(): bool
    {
        return !empty($this->errors);
    }


    /**
     * Get the (first) validation error
     */
    public function getError(): ?string
    {
        if (count($this->errors) > 1) {
            trigger_error("There are multiple errors, returning only the first", E_USER_NOTICE);
        }

        return reset($this->errors) ?: null;
    }

    /**
     * Get the validation errors
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Throw a validation exception if there are any errors
     */
    public function mustSucceed(): void
    {
        if ($this->failed()) {
            throw new ValidationException($this);
        }
    }


    /**
     * Factory method for successful validation
     */
    public static function success(): static
    {
        return new static();
    }

    /**
     * Factory method for failed validation
     */
    public static function error(string $message, mixed ...$args): static
    {
        $validation = new static();
        $validation->addError($message, ...$args);

        return $validation;
    }
}
