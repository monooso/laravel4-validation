<div>
    <img style="display: inline-block; margin-right: 10px;" src="https://travis-ci.org/experience/Validation.svg" data-bindattr-156="156" title="Build Status Images">
</div>

## Overview

This package makes it easy to implement custom validation for any given array of data in your application. It also solves a common problem whereby the validation rules for a "create" action differ to those for an "update" action.

For example, let's say we need to validate some "user account" data, both at the point of registration, and if the user chooses to modify their account at a later date. In Laravel, our (partial) validation rules might look something like this:

```php
$rules = ['username' => 'required|unique:users'];
```

That is, the value of the `username` field must be unique within the `users` table.

This is fine for account creation, but if the user updates his account without changing his username, the above validation rules will fail. The current `users` table already contains the given username, and the validation rules don't care that it belongs to the user being validated.

In Laravel, we deal with this problem by telling the validator to ignore the `id` of the current user, so our rules now look like this:

```php
// The user ID is 1234.
$rules = ['username' => 'required|unique:users,null,1234'];
```

The Validation package solves this problem by separating the "create" and "update" validation rules, and allowing you to use a `{key}` placeholder in your rules. Continuing with the above example, our validation rules now look like this:

```php
$createRules = ['username' => 'required|unique:users'];
$updateRules = ['username' => 'required|unique:users,null,{key}'];
```

More detailed implementation examples are provided in the "Usage" section, below.

## Installation

Install the package via [Composer][composer], as follows:

[composer]: http://getcomposer.org/

```js
"require": {
    "experience/validation": "~1.0"
}
```

If you're using Laravel, add the service provider to the `providers` array in your `app/config/app.php` file, as follows:

```php
'providers' => [
    // ...
    'Experience\Validation\ValidationServiceProvider',
];
```

## Usage

Let's assume you need to validate a registration form. First, create a custom "validator" class containing the necessary "create" and "update" rules. For example:

```php
<?php namespace Acme\Validators;

use Experience\Validation\Validators\Validator;

class RegistrationValidator extends Validator
{
    /**
     * Validation rules for creating an account.
     *
     * @var array
     */
    protected $createRules = [
        'username' => 'required|unique:users',
        'password' => 'required|min:10'
    ];
    
    /**
     * Validation rules for updating an account.
     *
     * @var array
     */
    protected $updateRules = [
        'username' => 'required|unique:users,null,{key}',
        'password' => 'required|min:10'
    ];
}
```

Next, inject an instance of your custom validator class into your controller, or wherever you perform your validation:

```php
use Acme\Validators\RegistrationValidator;
use Experience\Validation\Exceptions\ValidationException;

// ...

protected $validator;

public function __construct(RegistrationValidator $validator)
{
    $this->validator = $validator;
}

public function store()
{
    $input = Input::all();

    try {
        $this->validator->validateForCreate($input);
    } catch (ValidationException $e) {
        return Redirect::back()
            ->withInput()
            ->withErrors($e->getErrors());
    }
}

public function update($id)
{
    $input = Input::all();
		
    try {
        $this->validator->validateForUpdate($input, $id);
    } catch (ValidationException $e) {
        return Redirect::back()
            ->withInput()
            ->withErrors($e->getErrors());
    }
}
```

If validation passes, the `validate` method returns `true`. If validation fails, the `validate` method throws a `ValidationException` exception. You can catch the exception in your controller (as in the above example), or handle it globally in `global.php` if you prefer.

Create a dedicated class for each set of data you wish to validate. For example, if your newly-registered users can log in to your site, you'll probably want a `SessionValidator`:

```php
<?php namespace Acme\Validators;

use Experience\Validation\Validators\Validator;

class SessionValidator extends Validator
{
	protected $createRules = [
	    'username' => 'required',
	    'password' => 'required'
	];
	
	// No need to define the $updateRules, as a Session can't be 'updated'.
}
```

As before, inject an instance of this validator class into your controller or service, and call the appropriate "validate" method:

```php
$this->sessionValidator->validateForCreate(Input::all());
```

## Credits

This package was heavily influenced by [the Laracasts Validation package][laracasts_validation]. Whilst all the code was written from scratch, I owe a considerable debt of gratitude to Jeffrey Way for the general approach, and for his excellent tutorials at [Laracasts][laracasts].

Laracasts is hands-down the best learning resource available to PHP programmers (not just Laravel enthusiasts). If you're not already a subscriber, you should be.

[laracasts_validation]: https://github.com/laracasts/Validation
[laracasts]: http://laracasts.com