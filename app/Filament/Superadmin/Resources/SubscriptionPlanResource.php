<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationLabel = 'Plans';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-squares-2x2';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Pricing')
                ->schema([
                    TextInput::make('price_monthly')
                        ->label('Monthly Price')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                    TextInput::make('price_yearly')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                    Select::make('currency')
                        ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'UGX' => 'UGX', 'KES' => 'KES'])
                        ->default('USD')
                        ->required(),
                    TextInput::make('trial_days')
                        ->label('Trial Days')
                        ->numeric()
                        ->default(14),
                ])->columns(2),

            Section::make('Limits')
                ->schema([
                    TextInput::make('max_users')
                        ->label('Max Users')
                        ->numeric()
                        ->default(5),
                    TextInput::make('max_companies')
                        ->label('Max Companies')
                        ->numeric()
                        ->default(1),
                ])->columns(2),

            Section::make('Features')
                ->description('List of features to display on the pricing page')
                ->schema([
                    Repeater::make('features')
                        ->schema([
                            TextInput::make('value')->label('Feature')->required(),
                        ])
                        ->addActionLabel('Add Feature')
                        ->columnSpanFull()
                        ->reorderable()
                        ->cloneable(),
                ]),

            Section::make('Status')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    TextInput::make('sort')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('price_monthly')
                    ->label('Monthly')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('price_yearly')
                    ->label('Yearly')
                    ->money('USD'),
                TextColumn::make('trial_days')
                    ->label('Trial')
                    ->suffix(' days'),
                TextColumn::make('max_users')
                    ->label('Max Users'),
                TextColumn::make('activeSubscriptions_count')
                    ->label('Active Subs')
                    ->counts('activeSubscriptions'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->reorderable('sort');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit'   => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
