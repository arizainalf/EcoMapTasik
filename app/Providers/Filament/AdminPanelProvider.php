<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Setting;
use Filament\PanelProvider;
use App\Filament\Pages\Report;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
use Asmit\ResizedColumn\ResizedColumnPlugin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $setting = Setting::first(); // karena cuma ada satu baris
        $appName = $setting?->app_name ?? 'Default App';

        return $panel
            ->default()
            ->id('admin')
            // ->topNavigation()
            ->path('admin')
            ->brandName($appName)
            ->brandLogo(
            $setting && $setting->logo
                ? asset('storage/' . $setting->logo)
                : null // fallback jika tidak ada logo
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->brandLogoHeight('60px')
            ->darkMode(false)
            ->favicon(asset('storage/'.getSetting()->logo))
            ->navigationGroups([
                // 'Data Master',
                // 'Data Transaksi',
                // 'Laporan',
                'Settings',
            ])
            ->font('poppins')
            ->collapsibleNavigationGroups(false)
            ->colors([
                'primary' => $setting?->color ?? '#19fc00',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Report::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                'admin',
            ])
            ->plugins([
            // ... other plugins
            ResizedColumnPlugin::make()
                ->preserveOnDB() // Enable database storage (optional)
        ]);
    }
}
