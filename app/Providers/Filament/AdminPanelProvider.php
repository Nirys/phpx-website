<?php

namespace App\Providers\Filament;

use App\Models\Group;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->pages([
                Pages\Dashboard::class,
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
            ]);

        if (!app('phpx')->isGlobalSite() || app()->runningConsoleCommand()) {
            $panel
                ->discoverResources(in: app_path('Filament/GroupAdmin/Resources'), for: 'App\\Filament\\GroupAdmin\\Resources')
                ->discoverPages(in: app_path('Filament/GroupAdmin/Pages'), for: 'App\\Filament\\GroupAdmin\\Pages')
                ->tenant(Group::class, slugAttribute: 'domain')
                ->tenantMenu(false)
                ->domains(app('phpx')->getRoutingDomains())
                ->discoverWidgets(in: app_path('Filament/GroupAdmin/Widgets'), for: 'App\\Filament\\GroupAdmin\\Widgets')
                ->tenantDomain('{tenant:domain}');
        } else {
            $panel
                ->discoverWidgets(in: app_path('Filament/GlobalAdmin/Widgets'), for: 'App\\Filament\\GlobalAdmin\\Widgets')
                ->discoverResources(in: app_path('Filament/GlobalAdmin/Resources'), for: 'App\\Filament\\GlobalAdmin\\Resources')
                ->discoverPages(in: app_path('Filament/GlobalAdmin/Pages'), for: 'App\\Filament\\GlobalAdmin\\Pages');
        }

        return $panel
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
