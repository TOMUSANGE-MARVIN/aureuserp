<?php

namespace App\Filament\Superadmin\Resources\TenantResource\Pages;

use App\Filament\Superadmin\Resources\TenantResource;
use App\Services\TenantProvisioningService;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_tenant'] = true;

        return $data;
    }
}
