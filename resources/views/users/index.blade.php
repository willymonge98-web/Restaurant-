@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('subtitle', 'Comptes et journal d\'activité')
@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('users.create') }}" class="rounded-xl bg-lime-800 px-4 py-2 text-white">Nouvel utilisateur</a>
</div>
<div class="grid gap-6 xl:grid-cols-3">
    <div class="erp-card xl:col-span-2 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-50 text-left">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr class="border-t border-stone-100">
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->role?->label_fr }}</td>
                    <td class="px-4 py-3">{{ $user->is_active ? 'Actif' : 'Inactif' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('users.edit', $user) }}" class="text-lime-800">Modifier</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $users->links() }}</div>
    </div>
    <div class="erp-card p-4">
        <h2 class="font-display text-lg">Activité récente</h2>
        <ul class="mt-3 space-y-3 text-sm">
            @foreach ($logs as $log)
                <li class="rounded-lg bg-stone-50 p-3">
                    <div class="font-medium">{{ $log->action }}</div>
                    <div class="text-stone-500">{{ $log->user?->name }} · {{ $log->created_at->diffForHumans() }}</div>
                    <div>{{ $log->description }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
