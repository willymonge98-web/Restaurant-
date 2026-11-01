<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    public function index(): View
    {
        return view('tables.index', [
            'tables' => DiningTable::orderBy('zone')->orderBy('name')->get(),
            'statuses' => DiningTable::statusLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:dining_tables,code'],
            'capacity' => ['required', 'integer', 'min:1'],
            'zone' => ['nullable', 'string', 'max:100'],
        ]);

        DiningTable::create([...$data, 'status' => DiningTable::STATUS_LIBRE]);

        return back()->with('success', 'Table créée.');
    }

    public function updateStatus(Request $request, DiningTable $table): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:libre,occupee,reservee,en_nettoyage'],
        ]);

        $table->update($data);

        return back()->with('success', 'Statut table mis à jour.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        $table->delete();

        return back()->with('success', 'Table supprimée.');
    }
}
