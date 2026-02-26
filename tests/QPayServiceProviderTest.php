<?php

namespace QPay\Laravel\Tests;

use Orchestra\Testbench\TestCase;
use QPay\Laravel\QPayServiceProvider;
use QPay\QPayClient;

class QPayServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [QPayServiceProvider::class];
    }

    public function test_service_provider_is_registered(): void
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(QPayServiceProvider::class)
        );
    }

    public function test_config_is_merged(): void
    {
        $config = $this->app['config']->get('qpay');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('base_url', $config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);
        $this->assertArrayHasKey('invoice_code', $config);
        $this->assertArrayHasKey('callback_url', $config);
        $this->assertArrayHasKey('webhook_path', $config);
    }

    public function test_default_base_url(): void
    {
        $this->assertEquals(
            'https://merchant.qpay.mn',
            config('qpay.base_url')
        );
    }

    public function test_default_webhook_path(): void
    {
        $this->assertEquals(
            '/qpay/webhook',
            config('qpay.webhook_path')
        );
    }

    public function test_qpay_client_is_bound_as_singleton(): void
    {
        $this->app['config']->set('qpay.base_url', 'https://merchant.qpay.mn');
        $this->app['config']->set('qpay.username', 'test_user');
        $this->app['config']->set('qpay.password', 'test_pass');
        $this->app['config']->set('qpay.invoice_code', 'TEST_INVOICE');
        $this->app['config']->set('qpay.callback_url', 'https://example.com/callback');

        $client1 = $this->app->make(QPayClient::class);
        $client2 = $this->app->make(QPayClient::class);

        $this->assertInstanceOf(QPayClient::class, $client1);
        $this->assertSame($client1, $client2);
    }

    public function test_qpay_alias_resolves_to_client(): void
    {
        $this->app['config']->set('qpay.base_url', 'https://merchant.qpay.mn');
        $this->app['config']->set('qpay.username', 'test_user');
        $this->app['config']->set('qpay.password', 'test_pass');
        $this->app['config']->set('qpay.invoice_code', 'TEST_INVOICE');
        $this->app['config']->set('qpay.callback_url', 'https://example.com/callback');

        $client = $this->app->make('qpay');

        $this->assertInstanceOf(QPayClient::class, $client);
    }

    public function test_webhook_route_is_registered(): void
    {
        $route = $this->app['router']->getRoutes()->getByName('qpay.webhook');

        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
    }
}
