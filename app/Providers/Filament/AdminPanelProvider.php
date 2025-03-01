<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
        return $panel
            ->default()
            ->id('admin')
            ->brandName('Sim Obe Prodi Demo')
            ->darkMode(false)
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
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
            ->sidebarCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ])
            //footer
            // ->renderHook(
            //     // This line tells us where to render it
            //     'panels::body.end',
            //     // This is the view that will be rendered
            //     fn () => view('customFooter'))
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Pemetaan')
                    ->collapsible(true),
                NavigationGroup::make()
                    ->label('BK')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Profil Lulusan')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('CPL')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('CPMK')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Laporan')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Pengajar')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Mahasiswa')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Admin')
            ]);
    }

    public function boot()
    {
        FilamentView::registerRenderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, function () {
            return view('partials.login-popup');
        });
    }
}
