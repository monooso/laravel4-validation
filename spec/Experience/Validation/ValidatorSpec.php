<?php namespace spec\Experience\Validation;

use Experience\Validation\Exceptions\ValidationException;
use Experience\Validation\Interfaces\ValidationProviderInterface as ProviderInterface;
use Experience\Validation\Interfaces\ValidationProviderFactoryInterface as FactoryInterface;
use Experience\Validation\Validators\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory)
    {
        $this->beAnInstanceOf('spec\Experience\Validation\ExampleValidator');
        $this->beConstructedWith($factory);
    }

    public function it_validates_valid_creation_data(
        FactoryInterface $factory,
        ProviderInterface $validator
    ) {
        $data = ['username' => 'kate'];
        $rules = $this->getCreateRules();

        $factory->make($data, $rules, [])->willReturn($validator);
        $validator->fails()->willReturn(false);

        $this->validateForCreate($data)->shouldReturn(true);
    }

    public function it_throws_an_exception_with_invalid_creation_data(
        FactoryInterface $factory,
        ProviderInterface $validator
    ) {
        $data = ['username' => ''];
        $rules = $this->getCreateRules();
        $errors = ['Creation errors'];

        $factory->make($data, $rules, [])->willReturn($validator);
        $validator->fails()->willReturn(true);
        $validator->errors()->willReturn($errors);

        $exception = new ValidationException('Validation failed', $errors);

        $this->shouldThrow($exception)->duringValidateForCreate($data);
    }

    public function it_ignores_the_given_key_for_update()
    {
        $key = 1234;
        $updateRules = ['username' => 'required|unique:users,null,1234'];

        $this->getUpdateRules($key)->shouldReturn($updateRules);
    }

    public function it_validates_valid_update_data(
        FactoryInterface $factory,
        ProviderInterface $validator
    ) {
        $key = 1234;
        $data = ['username' => 'bob'];
        $rules = $this->getUpdateRules($key);

        $factory->make($data, $rules, [])->willReturn($validator);
        $validator->fails()->willReturn(false);

        $this->validateForUpdate($data, $key)->shouldReturn(true);
    }

    public function it_throws_an_exception_with_invalid_update_data(
        FactoryInterface $factory,
        ProviderInterface $validator
    ) {
        $key = 1234;
        $data = ['username' => ''];
        $rules = $this->getUpdateRules($key);
        $errors = ['Update errors'];

        $factory->make($data, $rules, [])->willReturn($validator);
        $validator->fails()->willReturn(true);
        $validator->errors()->willReturn($errors);

        $exception = new ValidationException('Validation failed', $errors);

        $this->shouldThrow($exception)->duringValidateForUpdate($data, $key);
    }
}


class ExampleValidator extends Validator
{
    protected $createRules = ['username' => 'required|unique:users'];
    protected $updateRules = ['username' => 'required|unique:users,null,{key}'];
}
