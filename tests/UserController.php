<?php

namespace LaravelFormRequest\Tests;

use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @param UserFormRequest $indexRequest
     */
    public function index(UserFormRequest $indexRequest)
    {
        $indexRequest->validateResolved();
    }

}
