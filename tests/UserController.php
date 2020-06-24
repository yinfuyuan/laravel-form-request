<?php

namespace LaravelFormRequest\Tests;

use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @param UserFormRequest $indexRequest
     * @return \Illuminate\Http\Response
     */
    public function index(UserFormRequest $indexRequest)
    {
        $indexRequest->validateResolved();
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param UserFormRequest $createRequest
     * @return \Illuminate\Http\Response
     */
    public function create(UserFormRequest $createRequest)
    {
        $createRequest->validateResolved();
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserFormRequest $storeRequest
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $storeRequest)
    {
        $storeRequest->validateResolved();
        //
    }

    /**
     * Display the specified resource.
     *
     * @param UserFormRequest $showRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserFormRequest $showRequest, $id)
    {
        $showRequest->validateResolved();
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserFormRequest $editRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserFormRequest $editRequest, $id)
    {
        $editRequest->validateResolved();
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserFormRequest $updateRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $updateRequest, $id)
    {
        $updateRequest->validateResolved();
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response();
    }

}
