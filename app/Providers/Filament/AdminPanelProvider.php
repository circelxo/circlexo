<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
// use Filament\Widgets;
// use BezhanSalleh\FilamentGoogleAnalytics\Widgets;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;
use Wave\Widgets;

class AdminPanelProvider extends PanelProvider
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-presentation-chart-line';
    }

    private $dynamicWidgets = [];

    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
            ->font(
                'Readex Pro',
                provider: GoogleFontProvider::class,
            )
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Rose,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\WaveInfoWidget::class,
                Widgets\WelcomeWidget::class,
                Widgets\UsersWidget::class,
                Widgets\PostsPagesWidget::class,
                ...$this->dynamicWidgets,
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
            ])
            ->favicon(asset('favicon.ico'))
            ->brandName('CircleXO')
            ->brandLogoHeight('25px')
            ->brandLogo(fn () => view('wave::admin.logo'))
            ->darkModeBrandLogo(fn () => view('wave::admin.logo-dark'));

        $panel->plugin(FilamentShieldPlugin::make());
        $panel->plugin(FilamentLanguageSwitcherPlugin::make());
        $panel->plugin(FilamentUsersPlugin::make());
        $panel->plugin(FilamentTypesPlugin::make());
        $panel->plugin(FilamentSettingsHubPlugin::make());
        $panel->plugin(FilamentMenusPlugin::make());
        $panel->plugin(
            FilamentAccountsPlugin::make()
                ->useTypes()
                ->useAvatar()
                ->canLogin()
                ->canBlocked()
                ->useNotifications()
                ->useExport()
                ->useImport()
        );
        $panel->plugin(
            FilamentAlertsPlugin::make()
                ->useSettingsHub()
        );

        return $panel;
    }

    // This function will render if user has account crenditals file
    // located at storage/app/analytics/service-account-credentials.json
    // Find More details here: https://github.com/spatie/laravel-analytics
    private function renderAnalyticsIfCredentialsExist()
    {
        if (file_exists(storage_path('app/analytics/service-account-credentials.json'))) {
            \Config::set('filament-google-analytics.page_views.filament_dashboard', true);
            \Config::set('filament-google-analytics.active_users_one_day.filament_dashboard', true);
            \Config::set('filament-google-analytics.active_users_seven_day.filament_dashboard', true);
            \Config::set('filament-google-analytics.active_users_twenty_eight_day.filament_dashboard', true);
            \Config::set('filament-google-analytics.most_visited_pages.filament_dashboard', true);
            \Config::set('filament-google-analytics.top_referrers_list.filament_dashboard', true);
        } else {
            $this->dynamicWidgets = [Widgets\AnalyticsPlaceholderWidget::class];
        }
    }
}
