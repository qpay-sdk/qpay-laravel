<?php

namespace QPay\Laravel\Tests;

use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use QPay\Laravel\Events\PaymentFailed;
use QPay\Laravel\Events\PaymentReceived;
use QPay\Laravel\QPayServiceProvider;
use QPay\Models\PaymentCheckResponse;
use QPay\QPayClient;

class WebhookControllerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [QPayServiceProvider::class];
    }

    public function test_webhook_returns_400_without_payment_id(): void
    {
        $response = $this->get(config('qpay.webhook_path'));

        $response->assertStatus(400);
        $this->assertSame('Missing qpay_payment_id', $response->getContent());
    }

    public function test_webhook_returns_success_when_payment_found(): void
    {
        Event::fake();

        $mockResult = new PaymentCheckResponse(
            count: 1,
            paidAmount: 1000.0,
            rows: [(object)['paymentId' => 'pay_123']],
        );

        $mock = $this->createMock(QPayClient::class);
        $mock->method('checkPayment')->willReturn($mockResult);
        $this->app->instance(QPayClient::class, $mock);

        $response = $this->get(config('qpay.webhook_path') . '?qpay_payment_id=pay_123');

        $response->assertStatus(200);
        $this->assertSame('SUCCESS', $response->getContent());

        Event::assertDispatched(PaymentReceived::class, function ($event) {
            return $event->paymentId === 'pay_123';
        });
    }

    public function test_webhook_returns_success_when_no_payment(): void
    {
        Event::fake();

        $mockResult = new PaymentCheckResponse(
            count: 0,
            paidAmount: 0.0,
            rows: [],
        );

        $mock = $this->createMock(QPayClient::class);
        $mock->method('checkPayment')->willReturn($mockResult);
        $this->app->instance(QPayClient::class, $mock);

        $response = $this->get(config('qpay.webhook_path') . '?qpay_payment_id=pay_456');

        $response->assertStatus(200);
        $this->assertSame('SUCCESS', $response->getContent());

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->paymentId === 'pay_456'
                && $event->reason === 'No payment found';
        });
    }

    public function test_webhook_returns_500_on_exception(): void
    {
        Event::fake();

        $mock = $this->createMock(QPayClient::class);
        $mock->method('checkPayment')->willThrowException(new \RuntimeException('API error'));
        $this->app->instance(QPayClient::class, $mock);

        $response = $this->get(config('qpay.webhook_path') . '?qpay_payment_id=pay_789');

        $response->assertStatus(500);
        $this->assertSame('FAILED', $response->getContent());

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->paymentId === 'pay_789'
                && $event->reason === 'API error';
        });
    }
}
