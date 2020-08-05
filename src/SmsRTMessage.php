<?php

namespace NotificationChannels\SmsRT;

class SmsRTMessage
{
    /** @var string */
    protected $content = null;

    public function __construct(string $content = null)
    {
        $this->content = $content;
    }

    /**
     * Set a content of the notification message.
     * @param  string  $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the notification message data.
     * @return null[]|string[]
     */
    public function getMessage(): array
    {
        return [
            'content' => $this->content,
        ];
    }
}
