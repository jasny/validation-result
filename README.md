Validation result
=================

A result object for a validation function.

## Installation

    composer require jasny/validation-result

## Examples

##### Validate variable

```php
use Jasny\ValidationResult;

function validateVar($var)
{
    if (isset($var)) return ValidationResult::error("Var isn't set");
    if ($var < 30) return ValidationResult::error("Var is less than thirty");
    
    return ValidationResult::success();
}

$validation = validateVar($myVar);
if ($validation->failed()) echo $validation->getError();
```

##### Validate POST request

```php
use Jasny\ValidationResult;

function validateInput($input)
{
    $result = new ValidationResult();

    if (!isset($input['baz'])) return ValidationResult::error("Baz isn't set");
    if (!isset($input['qux'])) return ValidationResult::error("Qux isn't set");
  
    return $result;
}

$validation = validateInput($_POST);

if ($validation->succeeded()) {
    // Handle POST and redirect
    exit();
}

loadTemplate('myTemplate', ['errors' => $validation->getErrors()]);
```

## Translation

It's possible to translate the error messages using a callback.

```php
use Jasny\ValidationResult;

$aliases = [
    "% isn't set" => "Please set %s",
    "% is to high" => "Please choose a lower value for %s"
];

ValidationResult::$translate = function($message) use ($aliases) {
    return isset($aliases[$message]) ? $aliases[$message] : $message;
};

function validateVar($var)
{
    if (isset($var)) return ValidationResult::error("%s isn't set", 'Var');
    if ($var < 30) return ValidationResult::error("%s is less than %d", 'Var', 30);
}
```

or simply

```php
ValidationResult::$translate = 'gettext';
```