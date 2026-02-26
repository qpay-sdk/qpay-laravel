<?php

namespace QPay\Laravel\Tests;

use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use QPay\Laravel\Events\PaymentFailed;
use QPay\Laravel\Events\PaymentReceived;
use QPay\Laravel\QPayServiceProvider;
use QPay\Models\PaymentCheckResponse;
use QPay\Models\PaymentDetail;
use QPay\QPayClient;

class WebhookControllerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [QPayServiceProvider::class];
    }

    public function test_webhook_returns_400_without_invoice_id(): void
    {
        $response = $this->postJson(config('qpay.webhook_path'));

        $response->assertStatus(400)
            ->assertJson(['error' => 'Missing invoice_id']);
    }

    public function test_webhook_returns_paid_when_payment_found(): void
    {
        Event::fake();

        $mockResult = new PaymentCheckResponse(
            count: 1,
            paidAmount: 1000.0,
            rows: [
                new PaymentDetail(
                    paymentId: 'pay_123',
                    paymentStatus: 'PAID',
                    paymentAmount: 1000.0,
                    paymentCurrency: 'MNT',
                    paymentWallet: 'qpay',
                    paymentType: 'P2P',
                    transactionId: 'txn_123',
                ),
            ],
        );

        $mock = $this->createMock(QPayClient::class);
        $mock->method('checkPayment')->willReturn($mockResult);
        $this->app->instance(QPayClient::class, $mock);

        $response = $this->postJson(config('qpay.webhook_path'), [
            'invoice_id' => 'inv_123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'paid']);

        Event::assertDispatched(PaymentReceived::class, function ($event) {
            return $event->invoiceId === 'inv_123';
        });
    }

    public function test_webhook_returns_unpaid_when_no_payment(): void
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

        $response = $this->postJson(config('qpay.webhook_path'), [
            'invoice_id' => 'inv_456',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'unpaid']);

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->invoiceId === 'inv_456'
                && $event->reason === 'No payment found';
        });
    }

    public function test_webhook_returns_500_on_exception(): void
    {
        Event::fake();

        $mock = $this->createMock(QPayClient::class);
        $mock->method('checkPayment')->willThrowException(new \RuntimeException('API error'));
        $this->app->instance(QPayClient::class, $mock);

        $response = $this->postJson(config('qpay.webhook_path'), [
            'invoice_id' => 'inv_789',
        ]);

        $response->assertStatus(500)
            ->assertJson(['error' => 'API error']);

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->invoiceId === 'inv_789'
                && $event->reason === 'API error';
        });
    }
}
