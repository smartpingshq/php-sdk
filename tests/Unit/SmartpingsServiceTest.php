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

    public function test_it_can_send_phone_verification()
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

        $response = $service->sendPhoneVerification('+15551234567', 'John Doe');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_verify_phone_with_code()
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

        $response = $service->verifyPhoneWithCode('+15551234567', '123456');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_send_email_verification()
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

        $response = $service->sendEmailVerification(
            'test@example.com',
            'John Doe',
            'https://example.com/verify'
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_verify_email_with_code()
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

        $response = $service->verifyEmailWithCode('test@example.com', 'abc123token');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_send_verification_with_custom_expiration_and_lists()
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

        $response = $service->sendPhoneVerification(
            '+15551234567',
            'John Doe',
            10,
            [1, 2, 3]
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_can_get_contact_verification_status()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'success' => true,
                'statusCode' => 200,
                'data' => [
                    'identifier' => 'test@example.com',
                    'contact_type' => 'email',
                    'name' => 'Test User',
                    'status' => 'pending',
                    'verified' => false,
                    'verified_at' => null,
                    'expires_at' => '2024-01-01T12:00:00Z',
                ],
            ])),
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

        $response = $service->getContactVerificationStatus('test@example.com');

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('test@example.com', $responseData['data']['identifier']);
        $this->assertEquals('pending', $responseData['data']['status']);
        $this->assertFalse($responseData['data']['verified']);
    }

    public function test_it_can_get_verified_contact_status()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'success' => true,
                'statusCode' => 200,
                'data' => [
                    'identifier' => '+15551234567',
                    'contact_type' => 'phone',
                    'name' => 'John Doe',
                    'status' => 'verified',
                    'verified' => true,
                    'verified_at' => '2024-01-01T10:00:00Z',
                    'expires_at' => '2024-01-01T12:00:00Z',
                ],
            ])),
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

        $response = $service->getContactVerificationStatus('+15551234567');

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('+15551234567', $responseData['data']['identifier']);
        $this->assertEquals('verified', $responseData['data']['status']);
        $this->assertTrue($responseData['data']['verified']);
    }

    private function service(MockHandler $mock): SmartpingsService
    {
        $httpFactory = new HttpFactory;

        return new SmartpingsService(
            new Client(['handler' => HandlerStack::create($mock)]),
            $httpFactory,
            $httpFactory,
            'https://example.com/',
            'test-client-id',
            'test-secret-id'
        );
    }

    public function test_it_can_send_a_whatsapp_text_message()
    {
        $mock = new MockHandler([new Response(200, [], json_encode(['data' => []]))]);
        $service = $this->service($mock);

        $response = $service->sendWhatsAppText('Your code is 1234', '+15551234567');

        $this->assertEquals(200, $response->getStatusCode());

        $request = $mock->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringEndsWith('v1/whatsapp/messages', (string) $request->getUri());
        $this->assertEquals('test-client-id', $request->getHeaderLine('X-Client-Id'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertEquals('text', $body['kind']);
        $this->assertEquals('Your code is 1234', $body['message']);
        $this->assertEquals(['+15551234567'], $body['destination']);
    }

    public function test_it_can_send_a_whatsapp_template_message()
    {
        $mock = new MockHandler([new Response(200, [], json_encode(['data' => []]))]);
        $service = $this->service($mock);

        $response = $service->sendWhatsAppTemplate('+15551234567', 'order_confirmation', 'en_US');

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string) $mock->getLastRequest()->getBody(), true);
        $this->assertEquals('template', $body['kind']);
        $this->assertEquals('order_confirmation', $body['template']['name']);
        $this->assertEquals('en_US', $body['template']['language']);
        $this->assertArrayNotHasKey('message', $body);
    }

    public function test_it_can_get_whatsapp_message_status()
    {
        $mock = new MockHandler([new Response(200, [], json_encode(['data' => ['status' => 'sent']]))]);
        $service = $this->service($mock);

        $response = $service->getWhatsAppMessageStatus('msg-slug-123');

        $this->assertEquals(200, $response->getStatusCode());

        $request = $mock->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertStringEndsWith('v1/whatsapp/messages/msg-slug-123', (string) $request->getUri());
    }
}
