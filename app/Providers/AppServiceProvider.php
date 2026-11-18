<?php

namespace App\Providers;

use App\Services\Payment\FakeGateway;
use App\Services\Payment\MidtransGateway;
use App\Services\Payment\PaymentGateway;
use App\Services\Shipping\FlatRateShippingService;
use App\Services\Shipping\ShippingService;
use InvalidArgumentException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShippingService::class, FlatRateShippingService::class);
        $this->app->bind(PaymentGateway::class, function ($app): PaymentGateway {
            return match ($app['config']->get('kreva.payment_driver')) {
                'fake' => $app->make(FakeGateway::class),
                'midtrans' => $app->make(MidtransGateway::class),
                default => throw new InvalidArgumentException('Payment driver is not supported.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
