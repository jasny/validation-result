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
    if (isset($var)) return ValidationResult::error("var isn't set");
    if ($var < 30) return ValidationResult::error("var is less than thirty");
    
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
    $validation = new ValidationResult();

    if (!isset($input['baz'])) $validation->addError("baz isn't set");
    if (!isset($input['qux'])) $validation->addError("qux isn't set");
  
    return $validation;
}

$validation = validateInput($_POST);

if ($validation->succeeded()) {
    // Handle POST and redirect
    exit();
}

loadTemplate('myTemplate', ['errors' => $validation->getErrors()]);
```

## Subvalidation
You can add the validation result of a subvalidation using the `add()` method. It's possible to prefix all the errors
of the subvalidation.

```php
use Jasny\ValidationResult;

function validateInput($input)
{
    $validation = new ValidationResult();

    if (!isset($input['baz'])) $validation->addError("baz isn't set");
    if (!isset($input['qux'])) $validation->addError("qux isn't set");
  
    if (isset($input['foo'])) {
        $fooValidation = validateFoo($input['foo']);
        $validation->add($fooValidation, 'foo');
    }
  
    return $validation;
}

function validateFoo($foo)
{
    $validation = new ValidationResult();
    
    if (empty($foo['name'])) $validation->addError("name isn't set");
    if (empty($foo['age'])) $validation->addError("age isn't set");
    
    return $validation;
}

$validation = validateInput($_POST);
```

## Translation

It's possible to translate the error messages using a callback.

```php
use Jasny\ValidationResult;

$aliases = [
    "%s isn't set" => 'Please set %s',
    "%s is less than %d" => 'Please choose a value higher than %2$d for %1$s'
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
