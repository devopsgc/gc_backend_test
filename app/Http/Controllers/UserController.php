<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('index', User::class);
        $query = User::query();
        if ($request->q) {
            $query->where('email', 'like', '%' . $request->q . '%')
            ->orWhere('first_name', 'like', '%' . $request->q . '%')
            ->orWhere('last_name', 'like', '%' . $request->q . '%');
        }
        $this->data['users'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $this->data['title'] = 'Manage Users';

        return view('userIndex', $this->data);
    }

    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        $this->data['countries'] = Country::getAllEnabledCountries();
        $this->data['roles'] = Role::whereIn('type', ['admin', 'manager', 'operations', 'sales'])->get();
        return view('userCreateEdit', $this->data);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        return $this->create_update($request, new User);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return redirect('users/' . $user->id . '/edit');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $this->data['user'] = $user;
        $this->data['countries'] = Country::getAllEnabledCountries();
        $this->data['roles'] = Role::whereIn('type', $user->isSuperAdmin() ? ['super_admin'] :
            ['admin', 'manager', 'operations', 'sales'])->get();
        return view('userCreateEdit', $this->data);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        return $this->create_update($request, $user);
    }

    public function create_update(Request $request, User $user)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:80', Rule::unique('users')->ignore($user->id)],
            'first_name' => 'required|string|max:40',
            'last_name' => 'required|string|max:40',
            'password' => $user->id ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'role_id' => 'required|integer',
            'country_ids.*' => 'nullable|string',
        ]);

        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        // Super admin role cannot be given or removed
        if (!$user->isSuperAdmin() && $request->role_id > 1) {
            $user->role_id = $request->role_id;
        }

        $user->save();
        $user->countries()->sync($request->country_ids);

        return redirect('users/' . $user->id . '/edit')->with('status', 'The data has been saved.');
    }

    public function suspend(User $user)
    {
        $this->authorize('suspend', $user);
        $user->suspended_at = Carbon::now();
        $user->save();

        return redirect('users');
    }

    public function restore(User $user)
    {
        $this->authorize('restore', $user);
        $user->suspended_at = null;
        $user->save();

        return redirect('users');
    }
}
