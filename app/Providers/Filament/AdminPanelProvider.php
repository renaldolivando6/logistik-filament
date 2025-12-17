<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render("@vite('resources/js/app.js')")
            )
            
            ->brandLogo(fn () => view('filament.brand'))
            ->brandName('PT. Trans Anugerah Nusa')
            
            ->colors([
                'primary' => Color::Amber,
            ])
            
            ->defaultAvatarProvider(\Filament\AvatarProviders\UiAvatarsProvider::class)

            ->sidebarCollapsibleOnDesktop()
            
            // ✅ SET NAVIGATION GROUP ORDER
            ->navigationGroups([
                NavigationGroup::make('Master Data')
                ->collapsed(),
                    
                NavigationGroup::make('Transaksi')->collapsed(),
                    
                NavigationGroup::make('Laporan')
                ->collapsed(),
                    
                NavigationGroup::make('Pengaturan')
                ->collapsed(),
            ])
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                \App\Filament\Widgets\DashboardStatsOverview::class,
                \App\Filament\Widgets\RecentOrdersWidget::class,
                \App\Filament\Widgets\QuickActions::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}