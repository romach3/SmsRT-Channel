<?php

namespace NotificationChannels\SmsRT\Test;

use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\SmsRT\SmsRTApi;
use NotificationChannels\SmsRT\SmsRTMessage;

class SmsRTApiTest extends BaseCase
{
    /**
     * @var Notification|Mockery\MockInterface
     */
    protected $testNotification;

    /** @test */
    public function it_can_be_instantiate()
    {
        $api = $this->resolveAPI();

        $this->assertInstanceOf(SmsRTApi::class, $api);
    }

    /** @test */
    public function it_sends_a_notification()
    {
        $api = $this->resolveAPI();

        $this->assertIsArray($api->smsSend($_ENV['SMSRT_RECIPIENT'] ?? 'TEST_RECIPIENT', new SmsRTMessage('TEST_BODY')));
    }

    /** @test */
    public function it_get_notification_status()
    {
        $api = $this->resolveAPI([
            new Response(200, [], json_encode(['status' => 'ok', 'result' => ['uid' => 'MESSAGE_ID']])),
            new Response(200, [], json_encode(['status' => 'ok', 'result' => ['status' => 'delivered']])),
        ]);

        $response = $api->smsSend($_ENV['SMSRT_RECIPIENT'] ?? 'TEST_RECIPIENT', new SmsRTMessage('TEST_BODY'));
        $this->assertArrayHasKey('uid', $response);
        $this->assertArrayHasKey('status', $api->smsStatus($response['uid']));
    }
}
