<?php namespace Experience\Validation\Interfaces;

interface ValidationProviderFactoryInterface
{
    /**
     * Initialise and return validator.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     *
     * @return ValidationProviderInterface
     */
    public function make(Array $data, Array $rules, Array $messages = []);
}