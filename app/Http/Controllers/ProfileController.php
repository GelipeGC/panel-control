<?php

namespace App\Http\Controllers;

use App\User;
use App\Profession;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        //TODO::auth user
        $user = User::first();

        return view('profile.edit', [
            'user' => $user,
            'professions' => Profession::orderBy('title')->get(), 
        ]);
    }

    public function update(Request $request)
    {
        //TODO::auth user
        $user = User::first();

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        $user->profile->update([
            'bio' => $request->bio,
            'twitter' => $request->twitter,
            'profession_id' => $request->profession_id,
        ]);

        return back();

    }
}
