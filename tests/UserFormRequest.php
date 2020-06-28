<?php

namespace LaravelFormRequest\Tests;

use LaravelFormRequest\FormRequest;

class UserFormRequest extends FormRequest
{

    public function defaultRules()
    {
        return [];
    }

    public function defaultMessages()
    {
        return [];
    }

    public function indexRules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function indexMessages()
    {
        return [
            'name.required' => 'The name field is required in index scenario.',
        ];
    }

    public function closureRules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function closureMessages()
    {
        return [
            'name.required' => 'The name field is required in closure scenario.',
        ];
    }

}
