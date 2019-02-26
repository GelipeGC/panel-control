<?php

namespace App\Http\Controllers;

use App\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\{Profession, Skill, User};
use Illuminate\Support\Facades\DB;
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};

class UserController extends Controller
{
    public function index(Request $request, UserFilter $filters)
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->filterBy($filters, $request->only(['state','role','search']))
            
            ->orderByDesc('created_at')
            ->paginate();
        $users->appends($filters->valid());
        return view('users.index', [
            'users' => $users,
            'view' => 'index',
            'showFilters' => true,
            'skills' => Skill::orderBy('name')->get(),
            'checkedSkills' => collect(request('skills')),
        ]);
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->orderBy('created_at','DESC')->paginate(15);


        return view('users.index', [
            'users' => $users,
            'view' => 'trash'
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        
        $user = new User();
        return view('users.create', compact('user'));
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request,User $user)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', ['user' => $user]);
    }

   

    public function trash(User $user)
    {
        $user->delete();

        $user->profile()->delete();

        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();
        
        
        $user = $user->forceDelete();

        return redirect()->route('users.trashed');
    }
}
