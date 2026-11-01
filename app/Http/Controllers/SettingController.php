<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RestaurantSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'settings' => RestaurantSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'currency_primary' => ['required', 'string', 'max:10'],
            'currency_secondary' => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'ticket_footer' => ['nullable', 'string', 'max:1000'],
            'tax_label' => ['nullable', 'string', 'max:100'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $settings = RestaurantSetting::current();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);
        $settings->update($data);

        ActivityLog::record('settings.update', $settings, 'Mise à jour paramètres restaurant');

        return back()->with('success', 'Paramètres enregistrés.');
    }
}
