<?php

namespace App\Filament\Superadmin\Resources\SubscriptionResource\Pages;

use App\Filament\Superadmin\Resources\SubscriptionResource;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()];
    }
}
