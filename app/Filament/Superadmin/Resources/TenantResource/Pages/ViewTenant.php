<?php

namespace App\Filament\Superadmin\Resources\TenantResource\Pages;

use App\Filament\Superadmin\Resources\TenantResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Organisation Details')
                ->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('slug'),
                    TextEntry::make('email'),
                    TextEntry::make('phone'),
                    TextEntry::make('website'),
                    TextEntry::make('subdomain'),
                ])->columns(3),

            Section::make('Subscription')
                ->schema([
                    TextEntry::make('subscription.plan.name')->label('Plan')->badge()->color('primary'),
                    TextEntry::make('subscription.status')
                        ->label('Status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'active'    => 'success',
                            'trial'     => 'info',
                            'suspended' => 'danger',
                            default     => 'gray',
                        }),
                    TextEntry::make('subscription.billing_cycle')->label('Billing'),
                    TextEntry::make('subscription.amount')->label('Amount')->money('USD'),
                    TextEntry::make('subscription.trial_ends_at')->label('Trial Ends')->dateTime(),
                    TextEntry::make('subscription.ends_at')->label('Renews')->dateTime(),
                ])->columns(3),

            Section::make('Status')
                ->schema([
                    IconEntry::make('is_active')->label('Active')->boolean(),
                    TextEntry::make('suspended_at')->label('Suspended At')->dateTime()->default('Not Suspended'),
                    TextEntry::make('suspension_reason')->label('Reason')->default('—'),
                    TextEntry::make('trial_ends_at')->label('Trial Ends')->dateTime()->default('—'),
                    TextEntry::make('created_at')->label('Joined')->dateTime(),
                ])->columns(3),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}

