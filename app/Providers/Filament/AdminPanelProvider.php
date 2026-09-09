<?php
namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\SetUserLocale;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Amber,
            ])
            ->font(fn (): string => App::getLocale() === 'ar' ? 'Tajawal' : 'Inter')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
                 SetUserLocale::class, 
            ]);
    }
}