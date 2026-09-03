<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data dari tabel settings dan format ke bentuk array key-value
        $settings = Setting::pluck('value', 'key')->toArray();

        // Pastikan default key selalu ada meskipun database baru/kosong
        $defaultSettings = [
            'app_name' => 'PM Control Center',
            'timezone' => 'Asia/Jakarta',
            'project_code_prefix' => 'PRJ-',
            'task_code_prefix' => 'TSK-',
            'milestone_code_prefix' => 'MLS-',
            'risk_code_prefix' => 'RSK-',
            'issue_code_prefix' => 'ISS-',
            'change_request_code_prefix' => 'CRQ-',
            'client_code_prefix' => 'CLI-',
            'vendor_code_prefix' => 'VND-',
            'maintenance_mode' => 'false',
        ];

        $mergedSettings = array_merge($defaultSettings, $settings);

        // Ambil preferensi user saat ini
        $user = $request->user();
        $defaultPreferences = [
            'theme' => 'system',
            'notify_task_assigned' => true,
            'notify_daily_digest' => false,
        ];
        $userPreferences = array_merge($defaultPreferences, $user?->preferences ?? []);

        return Inertia::render('Settings/Index', [
            'settings' => $mergedSettings,
            'user_preferences' => $userPreferences,
        ]);
    }

    public function updateGlobal(Request $request)
    {
        // RBAC: Hanya Admin yang diizinkan mengubah konfigurasi global
        if (!$request->user() || !$request->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized. Only Administrators can modify global system settings.');
        }

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            // Normalisasi boolean jika dikirim dalam bentuk boolean JS
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        return redirect()->back()->with('message', 'Global system settings saved successfully.');
    }

    public function updatePersonal(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'nullable|string|in:light,dark,system',
            'notify_task_assigned' => 'nullable|boolean',
            'notify_daily_digest' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $currentPreferences = $user->preferences ?? [];
        $user->preferences = array_merge($currentPreferences, $validated);
        $user->save();

        return redirect()->back()->with('message', 'Personal preferences saved successfully.');
    }

    public function update(Request $request)
    {
        return $this->updateGlobal($request);
    }
}
