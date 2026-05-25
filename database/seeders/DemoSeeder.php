<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name'      => 'Demo Company Ltd',
                'email'     => 'admin@democompany.com',
                'phone'     => '+1-555-0100',
                'is_active' => true,
                'is_tenant' => true,
                'subdomain' => 'demo',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'demo@democompany.com'],
            [
                'name'               => 'Demo Admin',
                'password'           => Hash::make('Demo@12345'),
                'is_active'          => true,
                'default_company_id' => $company->id,
            ]
        );
        $admin->allowedCompanies()->syncWithoutDetaching([$company->id]);

        $staff = User::firstOrCreate(
            ['email' => 'staff@democompany.com'],
            [
                'name'               => 'Demo Staff',
                'password'           => Hash::make('Demo@12345'),
                'is_active'          => true,
                'default_company_id' => $company->id,
            ]
        );
        $staff->allowedCompanies()->syncWithoutDetaching([$company->id]);

        $this->command->info("Demo company created: {$company->name} (ID: {$company->id})");
        $this->command->info('Demo admin: demo@democompany.com / Demo@12345');
        $this->command->info('Demo staff: staff@democompany.com / Demo@12345');
        $this->command->info('Note: Run module-specific seeders for full demo data.');
    }
}
