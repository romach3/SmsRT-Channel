<?php

namespace NotificationChannels\SmsRT\Test;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\SmsRT\SmsRTChannel;
use NotificationChannels\SmsRT\SmsRTMessage;

class SmsRTChannelTest extends BaseCase
{
    /**
     * @var Notification|Mockery\MockInterface
     */
    protected $testNotification;

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_be_instantiate()
    {
        $api = $this->resolveAPI();
        $instance = new SmsRTChannel($api);

        $this->assertInstanceOf(SmsRTChannel::class, $instance);
    }

    /** @test */
    public function it_sends_a_notification()
    {
        $api = $this->resolveAPI();
        $channel = new SmsRTChannel($api);
        $this->assertIsArray($channel->send(new TestNotifiable(), new TestNotification()));
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForSmsRT()
    {
        return $_ENV['SMSRT_RECIPIENT'] ?? 'TEST_RECIPIENT';
    }
}

class TestNotification extends Notification
{
    public function toSmsRT($notifiable)
    {
        return new SmsRTMessage('TEST_BODY');
    }
}
