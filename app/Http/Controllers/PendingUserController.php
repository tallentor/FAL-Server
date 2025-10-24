<?php

namespace App\Http\Controllers;

use App\Models\PendingUser;
use App\Http\Requests\StorePendingUserRequest;
use App\Http\Requests\UpdatePendingUserRequest;

class PendingUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendingUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PendingUser $pendingUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendingUserRequest $request, PendingUser $pendingUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PendingUser $pendingUser)
    {
        //
    }
}
