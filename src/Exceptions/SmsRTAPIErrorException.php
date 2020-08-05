<?php

namespace NotificationChannels\SmsRT\Exceptions;

class SmsRTAPIErrorException extends CouldNotSendNotification implements SmsRTException
{
    protected $response;

    public function __construct($message = '', $response = [])
    {
        $this->response = $response;

        parent::__construct($message);
    }
}
