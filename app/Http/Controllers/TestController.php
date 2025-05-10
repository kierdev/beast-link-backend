<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Read all.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Hello, World!'
        ]);
    }

    /**
     * Create.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Read by ID.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Delete.
     */
    public function destroy(string $id)
    {
        //
    }
}
