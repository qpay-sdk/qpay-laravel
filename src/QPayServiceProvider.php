<?php

namespace QPay\Laravel;

use Illuminate\Support\ServiceProvider;
use QPay\Config;
use QPay\QPayClient;

class QPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/qpay.php', 'qpay');

        $this->app->singleton(QPayClient::class, function ($app) {
            $config = new Config(
                baseUrl: config('qpay.base_url'),
                username: config('qpay.username'),
                password: config('qpay.password'),
                invoiceCode: config('qpay.invoice_code'),
                callbackUrl: config('qpay.callback_url'),
            );

            return new QPayClient($config);
        });

        $this->app->alias(QPayClient::class, 'qpay');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/qpay.php' => config_path('qpay.php'),
        ], 'qpay-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/qpay'),
        ], 'qpay-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'qpay');
        $this->loadViewComponentsFrom('QPay\\Laravel\\Components', 'qpay');
        $this->loadRoutesFrom(__DIR__ . '/../routes/qpay.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }

    private function loadViewComponentsFrom(string $namespace, string $prefix): void
    {
        $this->app->afterResolving('blade.compiler', function ($blade) use ($namespace, $prefix) {
            $blade->component($namespace . '\\QrCode', $prefix . '-qr-code');
            $blade->component($namespace . '\\PaymentButton', $prefix . '-payment-button');
        });
    }
}
