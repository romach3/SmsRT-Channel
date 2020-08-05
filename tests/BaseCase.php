<?php

namespace NotificationChannels\SmsRT\Test;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationChannels\SmsRT\SmsRTApi;
use PHPUnit\Framework\TestCase;

class BaseCase extends TestCase
{
    protected function setUp(): void
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();
        } catch (InvalidPathException $exception) {
        }
        parent::setUp();
    }

    protected function resolveAPI($handlers = []): SmsRTApi
    {
        if (($_ENV['SMSRT_USE_API'] ?? 'false') === 'true') {
            $testClient = new Client();
        } else {
            if (empty($handlers)) {
                $handlers = [
                    new Response(200, [], json_encode(['status' => 'ok', 'result' => []])),
                ];
            }
            $mock = new MockHandler($handlers);
            $handlerStack = HandlerStack::create($mock);
            $testClient = new Client(['handler' => $handlerStack]);
        }

        return new SmsRTApi(
            $_ENV['SMSRT_SHORTCODE'] ?? 'SHORTCODE',
            $_ENV['SMSRT_LOGIN'] ?? 'LOGIN',
            $_ENV['SMSRT_PASSWORD'] ?? 'PASSWORD',
            $testClient
        );
    }
}
