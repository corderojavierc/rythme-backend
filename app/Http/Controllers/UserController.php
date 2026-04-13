<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::query()->paginate(60);

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): void
    {
        //
    }
}
