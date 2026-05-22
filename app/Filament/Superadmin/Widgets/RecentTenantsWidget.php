<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Subscription;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Support\Models\Company;

class RecentTenantsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Organisations';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Company::query()
                    ->where('is_tenant', true)
                    ->with(['subscription.plan'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Organisation')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('subscription.plan.name')
                    ->label('Plan')
                    ->badge()
                    ->default('—'),
                TextColumn::make('subscription.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'trial'     => 'info',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        default     => 'warning',
                    })
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ]);
    }
}
