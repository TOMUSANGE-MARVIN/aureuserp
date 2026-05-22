<?php

namespace App\Providers\Filament;

use App\Filament\Superadmin\Pages\SuperadminDashboard;
use App\Filament\Superadmin\Resources\SubscriptionPlanResource;
use App\Filament\Superadmin\Resources\SubscriptionResource;
use App\Filament\Superadmin\Resources\TenantResource;
use App\Filament\Superadmin\Widgets\StatsOverview;
use App\Filament\Superadmin\Widgets\RecentTenantsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('superadmin')
            ->login()
            ->colors([
                'primary' => Color::Violet,
                'danger'  => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info'    => Color::Sky,
            ])
            ->brandLogo(asset('images/aura.png'))
            ->brandLogoHeight('2rem')
            ->brandName('AureusERP — Platform')
            ->favicon(asset('images/favicon.ico'))
            ->darkMode(false)
            ->topNavigation()
            ->pages([
                SuperadminDashboard::class,
            ])
            ->resources([
                TenantResource::class,
                SubscriptionPlanResource::class,
                SubscriptionResource::class,
            ])
            ->widgets([
                StatsOverview::class,
                RecentTenantsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web');
    }
}
