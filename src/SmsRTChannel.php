<?php

namespace NotificationChannels\SmsRT;

use Illuminate\Notifications\Notification;

class SmsRTChannel
{
    /** @var SmsRTApi */
    protected $api;

    public function __construct(SmsRTApi $api)
    {
        $this->api = $api;
    }

    /**
     * Send the given notification.
     * @param $notifiable
     * @param  Notification  $notification
     * @return array|null
     * @throws Exceptions\SmsRTAPIErrorException
     * @throws Exceptions\SmsRTRequestException
     * @throws Exceptions\SmsRTResponseException
     */
    public function send($notifiable, Notification $notification): ?array
    {
        if (! ($to = $this->getRecipients($notifiable, $notification))) {
            return null;
        }

        $message = $notification->{'toSmsRT'}($notifiable);

        if (is_string($message)) {
            $message = new SmsRTMessage($message);
        }

        return $this->sendMessage($to, $message);
    }

    protected function getRecipients($notifiable, Notification $notification): ?string
    {
        return $notifiable->routeNotificationFor('SmsRT', $notification);
    }

    /**
     * @param $recipient
     * @param  SmsRTMessage  $message
     * @return array
     * @throws Exceptions\SmsRTAPIErrorException
     * @throws Exceptions\SmsRTRequestException
     * @throws Exceptions\SmsRTResponseException
     */
    protected function sendMessage($recipient, SmsRTMessage $message): array
    {
        return $this->api->smsSend($recipient, $message);
    }
}
