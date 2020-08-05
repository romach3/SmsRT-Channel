<?php

namespace NotificationChannels\SmsRT;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class SmsRTServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(SmsRTApi::class, function ($app) {
            return new SmsRTApi(
                $app['config']['services.SmsRT.shortcode'],
                $app['config']['services.SmsRT.login'],
                $app['config']['services.SmsRT.password']
            );
        });

        $this->app->bind(SmsRTChannel::class, function ($app) {
            return new SmsRTChannel(
                $this->app->make(SmsRTApi::class)
            );
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('SmsRT', function ($app) {
                return $this->app->make(SmsRTChannel::class);
            });
        });
    }
}
