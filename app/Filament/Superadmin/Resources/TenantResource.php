<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\TenantResource\Pages;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\Support\Models\Company;

class TenantResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationLabel = 'Organisations';

    protected static ?string $modelLabel = 'Organisation';

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('is_tenant', true)->with(['subscription.plan']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Organisation Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('email')
                        ->email(),
                    TextInput::make('phone'),
                    TextInput::make('website'),
                    TextInput::make('subdomain')
                        ->unique(ignoreRecord: true)
                        ->helperText('Used for tenant routing e.g. mycompany.yourdomain.com'),
                ])->columns(2),

            Section::make('Status')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    DateTimePicker::make('trial_ends_at')
                        ->label('Trial Ends At'),
                    DateTimePicker::make('suspended_at')
                        ->label('Suspended At'),
                    TextInput::make('suspension_reason')
                        ->label('Suspension Reason'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Organisation')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Company $record) => $record->email),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('subscription.plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color('primary')
                    ->default('—'),
                TextColumn::make('subscription.status')
                    ->label('Sub Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'trial'     => 'info',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        'past_due'  => 'warning',
                        default     => 'gray',
                    })
                    ->default('no subscription'),
                TextColumn::make('subscription.trial_ends_at')
                    ->label('Trial Ends')
                    ->since()
                    ->default('—'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                IconColumn::make('suspended_at')
                    ->label('Suspended')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (Company $record) => ! is_null($record->suspended_at)),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->label('Status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->query(fn ($query, $data) => $data['value']
                        ? $query->whereHas('subscription', fn ($q) => $q->where('status', $data['value']))
                        : $query
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Company $record) => is_null($record->suspended_at))
                    ->form([
                        TextInput::make('suspension_reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(function (Company $record, array $data): void {
                        $record->update([
                            'suspended_at'       => now(),
                            'suspension_reason'  => $data['suspension_reason'],
                        ]);
                        $record->subscription?->update(['status' => 'suspended']);
                        Notification::make()->title('Organisation suspended')->danger()->send();
                    }),
                Action::make('unsuspend')
                    ->label('Unsuspend')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Company $record) => ! is_null($record->suspended_at))
                    ->action(function (Company $record): void {
                        $record->update(['suspended_at' => null, 'suspension_reason' => null]);
                        $record->subscription?->update(['status' => 'active']);
                        Notification::make()->title('Organisation unsuspended')->success()->send();
                    }),
                Action::make('assign_plan')
                    ->label('Assign Plan')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->form([
                        Select::make('plan_id')
                            ->label('Plan')
                            ->options(SubscriptionPlan::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        Select::make('billing_cycle')
                            ->options(['monthly' => 'Monthly', 'yearly' => 'Yearly'])
                            ->default('monthly')
                            ->required(),
                        Select::make('status')
                            ->options(['trial' => 'Trial', 'active' => 'Active'])
                            ->default('active')
                            ->required(),
                    ])
                    ->action(function (Company $record, array $data): void {
                        $plan = SubscriptionPlan::find($data['plan_id']);
                        $record->subscriptions()->create([
                            'plan_id'       => $data['plan_id'],
                            'status'        => $data['status'],
                            'billing_cycle' => $data['billing_cycle'],
                            'amount'        => $data['billing_cycle'] === 'monthly' ? $plan->price_monthly : $plan->price_yearly,
                            'currency'      => $plan->currency,
                            'starts_at'     => now(),
                            'trial_ends_at' => $data['status'] === 'trial' ? now()->addDays($plan->trial_days) : null,
                        ]);
                        Notification::make()->title('Plan assigned')->success()->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit'   => Pages\EditTenant::route('/{record}/edit'),
            'view'   => Pages\ViewTenant::route('/{record}'),
        ];
    }
}

