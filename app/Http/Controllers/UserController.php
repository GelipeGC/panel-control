<?php

namespace App\Http\Controllers;

use App\Sortable;
use App\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index(Request $request, Sortable $sortable)
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->withLastLogin()
            ->onlyTrashedIf($request->routeIs('users.trashed'))
            ->applyFilters()
            ->orderByDesc('created_at')
            ->paginate();

        $sortable->appends($users->parameters());

        return view('users.index', [
            'users' => $users,
            'view' => $request->routeIs('users.trashed') ? 'trash' : 'index',
            'showFilters' => true,
            'skills' => Skill::orderBy('name')->get(),
            'checkedSkills' => collect(request('skills')),
            'sortable'  => $sortable
        ]);
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->orderBy('created_at', 'DESC')->paginate(15);


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

    public function update(UpdateUserRequest $request, User $user)
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
