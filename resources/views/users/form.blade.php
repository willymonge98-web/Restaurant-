@extends('layouts.app')
@section('title', $user->exists ? 'Modifier utilisateur' : 'Nouvel utilisateur')
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="erp-card max-w-xl space-y-4 p-6">
    @csrf
    @if ($user->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-medium">Nom</label>
        <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
    </div>
    <div>
        <label class="text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
    </div>
    <div>
        <label class="text-sm font-medium">Téléphone</label>
        <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
    </div>
    <div>
        <label class="text-sm font-medium">Rôle</label>
        <select name="role_id" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->label_fr }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Mot de passe {{ $user->exists ? '(laisser vide pour conserver)' : '' }}</label>
        <input type="password" name="password" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" {{ $user->exists ? '' : 'required' }}>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))> Actif
    </label>
    <button class="rounded-xl bg-lime-800 px-4 py-2.5 text-white">Enregistrer</button>
</form>
@endsection
