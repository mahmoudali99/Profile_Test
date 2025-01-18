<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $profile = Profile::first(); // Assuming we're working with a single profile
        return view('profile.index', compact('profile'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $profile = Profile::first();
        $profile->update($request->all());
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }
}