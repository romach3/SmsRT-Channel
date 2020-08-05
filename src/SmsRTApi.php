<?php

namespace NotificationChannels\SmsRT;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use NotificationChannels\SmsRT\Exceptions\SmsRTAPIErrorException;
use NotificationChannels\SmsRT\Exceptions\SmsRTRequestException;
use NotificationChannels\SmsRT\Exceptions\SmsRTResponseException;
use Psr\Http\Message\ResponseInterface;

class SmsRTApi
{
    /** @var string */
    protected $login;

    /** @var string */
    protected $password;

    /** @var string */
    protected $shortcode;

    /** @var Client */
    protected $client;

    protected $endpoint = 'https://sms.rt.ru/api/v2/send_message';

    public function __construct(string $shortcode, string $login, string $password, Client $client = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->shortcode = $shortcode;
        $this->client = $client ?? new Client([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Send the given SMS.
     * @param  string  $to
     * @param  SmsRTMessage  $message
     * @return array
     * @throws SmsRTAPIErrorException
     * @throws SmsRTRequestException
     * @throws SmsRTResponseException
     */
    public function smsSend(string $to, SmsRTMessage $message): array
    {
        return $this->sendPostRequest([
            'msisdn' => $to,
            'message_text' => $message->getMessage()['content'],
            'shortcode' => $this->shortcode,
        ]);
    }

    /**
     * Get SMS status.
     * @param string  $messageId
     * @return array
     * @throws SmsRTAPIErrorException
     * @throws SmsRTRequestException
     * @throws SmsRTResponseException
     */
    public function smsStatus(string $messageId): array
    {
        return $this->sendGetRequest([], '/'.$messageId);
    }

    /**
     * @param  array  $params
     * @param  string  $additionalPath
     * @return array
     * @throws SmsRTAPIErrorException
     * @throws SmsRTRequestException
     * @throws SmsRTResponseException
     */
    protected function sendPostRequest(array $params, $additionalPath = ''): array
    {
        try {
            $response = $this->client->post($this->endpoint.$additionalPath, [
                RequestOptions::HEADERS => [
                    'Authorization' => $this->getEncodedAuthToken(),
                ],
                RequestOptions::FORM_PARAMS => $params,
            ]);

            return $this->parseResponse($response);
        } catch (GuzzleException $guzzleException) {
            throw new SmsRTRequestException($guzzleException->getMessage(), $guzzleException->getCode(),
                $guzzleException);
        }
    }

    /**
     * @param  array  $params
     * @param  string  $additionalPath
     * @return array
     * @throws SmsRTAPIErrorException
     * @throws SmsRTRequestException
     * @throws SmsRTResponseException
     */
    protected function sendGetRequest(array $params, $additionalPath = ''): array
    {
        try {
            $response = $this->client->get($this->endpoint.$additionalPath, [
                RequestOptions::HEADERS => [
                    'Authorization' => $this->getEncodedAuthToken(),
                ],
                RequestOptions::QUERY => $params,
            ]);

            return $this->parseResponse($response);
        } catch (GuzzleException $guzzleException) {
            throw new SmsRTRequestException($guzzleException->getMessage(), $guzzleException->getCode(),
                $guzzleException);
        }
    }

    /**
     * @param  ResponseInterface  $response
     * @return array
     * @throws SmsRTAPIErrorException
     * @throws SmsRTResponseException
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

        if (isset($data['status']) && $data['status'] === 'ok' && isset($data['result'])) {
            return $data['result'];
        } elseif (isset($data['status']) && $data['status'] === 'error') {
            throw new SmsRTAPIErrorException('Message not delivered with error.', $data);
        }

        throw new SmsRTResponseException('Message not delivered with unknown error.');
    }

    protected function getEncodedAuthToken(): string
    {
        return 'Basic '.base64_encode($this->login.'.'.$this->password);
    }
}
