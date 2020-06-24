<?php

namespace LaravelFormRequest\Tests;

use LaravelFormRequest\FormRequest;

class UserFormRequest extends FormRequest
{

    public function defaultRules()
    {

    }

    public function defaultMessages()
    {

    }

    public function indexRules()
    {

    }

    public function indexMessages()
    {

    }

    public function createRules()
    {

    }

    public function createMessages()
    {

    }

    public function closureRules()
    {
        return [
            'scenario' => 'required',
        ];
    }

    public function closureMessages()
    {
        return [
            'scenario.required' => 'You use closure scenario',
        ];
    }

}
