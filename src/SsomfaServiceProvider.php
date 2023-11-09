<?php

namespace Thuleen\Ssomfa;

use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Thuleen\Ssomfa\Commands\SsomfaCommand;
use Thuleen\SsoMfa\Composers\ContractComposer;

class SsomfaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ssomfa')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ssomfa_table')
            ->hasCommand(SsomfaCommand::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ssomfa');
        View::composer('ssomfa::pendacc', ContractComposer::class);
    }

    protected $routeMiddleware = [
        'ssomfa' => \Thuleen\Ssomfa\Http\Middleware\SsoMfaMiddleware::class,
    ];
}
