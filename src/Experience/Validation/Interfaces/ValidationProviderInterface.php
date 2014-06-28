<?php namespace Experience\Validation\Interfaces;

interface ValidationProviderInterface
{
    public function fails();
    public function errors();
}