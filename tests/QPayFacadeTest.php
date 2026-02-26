<?php

namespace QPay\Laravel\Tests;

use Orchestra\Testbench\TestCase;
use QPay\Laravel\Facades\QPay;
use QPay\Laravel\QPayServiceProvider;
use QPay\QPayClient;

class QPayFacadeTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [QPayServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'QPay' => QPay::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('qpay.base_url', 'https://merchant.qpay.mn');
        $app['config']->set('qpay.username', 'test_user');
        $app['config']->set('qpay.password', 'test_pass');
        $app['config']->set('qpay.invoice_code', 'TEST_INVOICE');
        $app['config']->set('qpay.callback_url', 'https://example.com/callback');
    }

    public function test_facade_accessor_returns_qpay_client_class(): void
    {
        $this->assertEquals(
            QPayClient::class,
            QPay::getFacadeAccessor()
        );
    }

    public function test_facade_resolves_to_qpay_client(): void
    {
        $resolved = QPay::getFacadeRoot();

        $this->assertInstanceOf(QPayClient::class, $resolved);
    }

    public function test_facade_resolves_same_singleton(): void
    {
        $first = QPay::getFacadeRoot();
        $second = QPay::getFacadeRoot();

        $this->assertSame($first, $second);
    }

    public function test_facade_is_registered_as_alias(): void
    {
        $this->assertTrue(class_exists(\QPay\Laravel\Facades\QPay::class));
    }
}
