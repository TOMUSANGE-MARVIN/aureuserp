<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Support\Models\Company;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationLabel = 'Subscriptions';

    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-credit-card';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('company_id')
                    ->label('Organisation')
                    ->options(Company::where('is_tenant', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('plan_id')
                    ->label('Plan')
                    ->options(SubscriptionPlan::where('is_active', true)->pluck('name', 'id'))
                    ->required(),
                Select::make('status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Active',
                        'past_due'  => 'Past Due',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Select::make('billing_cycle')
                    ->options(['monthly' => 'Monthly', 'yearly' => 'Yearly'])
                    ->required(),
                TextInput::make('amount')->numeric()->prefix('$'),
                Select::make('currency')
                    ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'UGX' => 'UGX', 'KES' => 'KES'])
                    ->default('USD'),
                DateTimePicker::make('trial_ends_at')->label('Trial Ends'),
                DateTimePicker::make('starts_at')->label('Starts At'),
                DateTimePicker::make('ends_at')->label('Ends At'),
                DateTimePicker::make('cancelled_at')->label('Cancelled At'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Organisation')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'trial'     => 'info',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        'past_due'  => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('billing_cycle')->badge(),
                TextColumn::make('amount')->money('USD'),
                TextColumn::make('trial_ends_at')->label('Trial Ends')->since()->default('—'),
                TextColumn::make('ends_at')->label('Renews')->since()->default('—'),
                TextColumn::make('created_at')->label('Created')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Active',
                        'past_due'  => 'Past Due',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
