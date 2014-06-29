<?php namespace Experience\Validation\Validators;

use Experience\Validation\Exceptions\ValidationException;
use Experience\Validation\Interfaces\ValidatorInterface;
use Experience\Validation\Interfaces\ValidationProviderFactoryInterface as Factory;

abstract class Validator implements ValidatorInterface
{
    protected $createRules = [];
    protected $updateRules = [];
    protected $createMessages = [];
    protected $updateMessages = [];

    protected $factory;
    protected $validator;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function validateForCreate(Array $data)
    {
        return $this->validate(
            $data, $this->getCreateRules(), $this->getCreateMessages());
    }

    public function validateForUpdate(Array $data, $key = null)
    {
        return $this->validate(
            $data, $this->getUpdateRules($key), $this->getUpdateMessages());
    }

    protected function validate(Array $data, Array $rules, Array $messages)
    {
        $validator = $this->factory->make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException(
                'Validation failed', $validator->errors());
        } else {
            return true;
        }
    }

    public function getCreateRules()
    {
        return $this->createRules;
    }

    public function getCreateMessages()
    {
        return $this->createMessages;
    }

    public function getUpdateRules($key)
    {
        return $this->parseRules($this->updateRules, $key);
    }

    protected function parseRules($rules, $key)
    {
        array_walk($rules, function (&$fieldRules, $field) use ($key) {
            $fieldRules = str_replace('{key}', $key, $fieldRules);
        });

        return $rules;
    }

    public function getUpdateMessages()
    {
        return $this->updateMessages;
    }
}
