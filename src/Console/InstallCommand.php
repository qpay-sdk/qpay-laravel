<?php

namespace QPay\Laravel\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'qpay:install';
    protected $description = 'Install QPay payment integration';

    public function handle(): int
    {
        $this->info('Installing QPay...');

        $this->call('vendor:publish', ['--tag' => 'qpay-config']);

        $this->info('');
        $this->info('QPay installed! Add these to your .env:');
        $this->line('  QPAY_BASE_URL=https://merchant.qpay.mn');
        $this->line('  QPAY_USERNAME=your_username');
        $this->line('  QPAY_PASSWORD=your_password');
        $this->line('  QPAY_INVOICE_CODE=your_invoice_code');
        $this->line('  QPAY_CALLBACK_URL=https://yoursite.com/qpay/webhook');

        return self::SUCCESS;
    }
}
