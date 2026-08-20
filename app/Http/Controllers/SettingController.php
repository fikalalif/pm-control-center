<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::find($setting['id'])->update(['value' => $setting['value']]);
        }

        return redirect()->back()->with('message', 'Settings updated successfully.');
    }
}
