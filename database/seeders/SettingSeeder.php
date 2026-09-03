<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            ['group' => 'general', 'key' => 'app_name', 'value' => 'PM Control Center', 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'type' => 'string'],

            // Module Code Prefixes
            ['group' => 'prefixes', 'key' => 'project_code_prefix', 'value' => 'PRJ-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'task_code_prefix', 'value' => 'TSK-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'milestone_code_prefix', 'value' => 'MLS-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'risk_code_prefix', 'value' => 'RSK-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'issue_code_prefix', 'value' => 'ISS-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'change_request_code_prefix', 'value' => 'CRQ-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'client_code_prefix', 'value' => 'CLI-', 'type' => 'string'],
            ['group' => 'prefixes', 'key' => 'vendor_code_prefix', 'value' => 'VND-', 'type' => 'string'],

            // System Control
            ['group' => 'system', 'key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
