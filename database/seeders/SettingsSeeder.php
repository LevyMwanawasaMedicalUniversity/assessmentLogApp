<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize the current academic year setting
        Setting::setCurrentAcademicYear(date('Y'));

        // Add other default settings as needed
        Setting::set(
            'app_name',
            'Assessment Log App',
            'Application Name',
            'The name of the application',
            'text',
            true
        );

        Setting::set(
            'institution_name',
            'Levy Mwanawasa Medical University',
            'Institution Name',
            'The name of the institution',
            'text',
            true
        );
    }
}
