<?php

namespace App\Filament\Superadmin\Pages;

use App\Filament\Superadmin\Widgets\StatsOverview;
use App\Filament\Superadmin\Widgets\RecentTenantsWidget;
use Filament\Pages\Dashboard;

class SuperadminDashboard extends Dashboard
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-home';
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RecentTenantsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
