<?php

namespace Smartpings\Messaging\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Smartpings\Messaging\SmartpingsService;

class SmartpingsServiceTest extends TestCase
{
    public function test_it_can_send_a_single_sms()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['status' => 'success'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $httpFactory = new HttpFactory;

        $service = new SmartpingsService(
            $client,
            $httpFactory,
            $httpFactory,
            'https://example.com',
            'test-client-id',
            'test-secret-id'
        );

        $response = $service->sendSms('Test message', '+15551234567');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_send_sms_to_multiple_recipients()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['status' => 'success'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $httpFactory = new HttpFactory;

        $service = new SmartpingsService(
            $client,
            $httpFactory,
            $httpFactory,
            'https://example.com',
            'test-client-id',
            'test-secret-id'
        );

        $response = $service->sendSms('Test message', ['+15551234567', '+15557654321']);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
