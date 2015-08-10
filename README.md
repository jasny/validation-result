Validation result
=================

A result object for a validation function.

## Installation

composer require jasny/validation-result

## Usage

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

loadTemplate('myTemplate', ['errors' => $validation->getErrors()];
```
