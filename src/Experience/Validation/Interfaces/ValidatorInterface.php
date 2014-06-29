<?php namespace Experience\Validation\Interfaces;

interface ValidatorInterface
{
    public function validateForCreate(Array $data);
    public function validateForUpdate(Array $data);

    public function getCreateRules();
    public function getUpdateRules($key);

    public function getCreateMessages();
    public function getUpdateMessages();
}
