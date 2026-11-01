<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('role')->orderBy('name')->paginate(20),
            'logs' => ActivityLog::with('user')->latest()->limit(15)->get(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(['is_active' => true]),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = User::create($data);
        ActivityLog::record('user.create', $user, 'Création utilisateur '.$user->email);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $user): View
    {
        return view('users.form', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        ActivityLog::record('user.update', $user, 'Mise à jour utilisateur '.$user->email);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        ActivityLog::record('user.delete', $user, 'Suppression utilisateur '.$user->email);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
