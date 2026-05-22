<?php

namespace App\Filament\Superadmin\Resources\SubscriptionPlanResource\Pages;

use App\Filament\Superadmin\Resources\SubscriptionPlanResource;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionPlan extends EditRecord
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()];
    }
}
