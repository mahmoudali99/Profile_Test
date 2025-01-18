<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        return view('profile.index', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = Profile::first();
        if (!$profile) {
            $profile = new Profile();
        }
        
        if ($request->has('avatar')) {
            $file = $request->file('avatar');
            $filename = $file->getClientOriginalName(); // Get the original name
            $path = $file->storeAs('avatars', $filename, 'public'); // Save in the 'public/avatars' directory
            $profile->avatar = $path; // Save the file path in the database
        }

        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->birthday = $request->birthday;
        $profile->bio = json_decode($request->bio);
        $profile->save();
        return response()->json(['success' => true, 'message' => 'Profile updated successfully', 'data' => $profile],200);
    }
}

