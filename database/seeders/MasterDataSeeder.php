<?php

namespace Database\Seeders;

use App\Models\ProjectPhase;
use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Project Types
        $types = [
            ['name' => 'Internal', 'description' => 'Project internal perusahaan'],
            ['name' => 'External', 'description' => 'Project dari client luar'],
            ['name' => 'Retainer', 'description' => 'Project maintenance bulanan'],
        ];

        foreach ($types as $type) {
            ProjectType::create($type);
        }

        // 2. Seed Project Phases
        $phases = [
            ['name' => 'Initiation', 'description' => 'Fase inisiasi dan kick-off'],
            ['name' => 'Planning', 'description' => 'Fase perencanaan dan desain'],
            ['name' => 'Execution', 'description' => 'Fase pengerjaan atau development'],
            ['name' => 'Monitoring', 'description' => 'Fase pengawasan dan QA'],
            ['name' => 'Closure', 'description' => 'Fase serah terima dan penutupan'],
        ];

        foreach ($phases as $phase) {
            ProjectPhase::create($phase);
        }

        // 3. Seed Default Settings
        $settings = [
            ['group' => 'General', 'key' => 'company_name', 'value' => 'HETRA PM', 'type' => 'string'],
            ['group' => 'General', 'key' => 'support_email', 'value' => 'support@hetra.local', 'type' => 'string'],
            ['group' => 'Notifications', 'key' => 'email_notifications', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'Notifications', 'key' => 'slack_notifications', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'Preferences', 'key' => 'default_currency', 'value' => 'IDR', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }
    }
}
