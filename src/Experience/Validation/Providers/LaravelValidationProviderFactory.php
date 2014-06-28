<?php namespace Experience\Validation\Providers;

use Illuminate\Validation\Factory;
use Experience\Validation\Interfaces\ValidationProviderFactoryInterface;

class LaravelValidationProviderFactory implements ValidationProviderFactoryInterface
{
    protected $validator;

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Initialise and return validator.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     *
     * @return \Illuminate\Validation\Validator
     */
    public function make(Array $data, Array $rules, Array $messages = [])
    {
        return $this->validator->make($data, $rules, $messages);
    }
}