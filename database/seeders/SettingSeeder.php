<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // 1. Workspace & Company Profile
            ['group' => 'Company Profile', 'key' => 'company_name', 'value' => 'PM Control Center Inc.', 'type' => 'string'],
            ['group' => 'Company Profile', 'key' => 'company_email', 'value' => 'admin@pmcontrol.com', 'type' => 'string'],
            ['group' => 'Company Profile', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'type' => 'timezone'],

            // 2. UI/UX & Preferences
            ['group' => 'UI Preferences', 'key' => 'default_theme', 'value' => 'system', 'type' => 'theme'],
            ['group' => 'UI Preferences', 'key' => 'auto_collapse_sidebar', 'value' => '0', 'type' => 'boolean'],

            // 3. Notifications & Integrations
            ['group' => 'Notifications', 'key' => 'email_alerts', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'Notifications', 'key' => 'slack_webhook_url', 'value' => '', 'type' => 'string'],

            // 4. Project Defaults
            ['group' => 'Project Defaults', 'key' => 'project_id_prefix', 'value' => 'PRJ-', 'type' => 'string'],
            ['group' => 'Project Defaults', 'key' => 'task_id_prefix', 'value' => 'TSK-', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
