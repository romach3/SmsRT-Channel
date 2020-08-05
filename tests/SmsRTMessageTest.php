<?php

namespace NotificationChannels\SmsRT\Test;

use NotificationChannels\SmsRT\SmsRTMessage;

class SmsRTMessageTest extends BaseCase
{
    /** @test */
    public function it_can_be_instantiate()
    {
        $instance = new SmsRTMessage('TEST_BODY');
        $this->assertInstanceOf(SmsRTMessage::class, $instance);
    }

    /** @test */
    public function it_return_message_array()
    {
        $instance = new SmsRTMessage('TEST_BODY');
        $this->assertArrayHasKey('content', $instance->getMessage());
    }
}
