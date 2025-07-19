<?php

namespace Smartpings\Messaging;

use Exception;
use Psr\Http\Client\HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class SmartpingsService extends LoggingService
{
    public function __construct(
        protected HttpClientInterface $client,
        protected RequestFactoryInterface $requestFactory,
        protected StreamFactoryInterface $streamFactory,
        protected string $apiUrl,
        protected string $clientId,
        protected string $secretId
    ) {}

    /**
     * @throws Exception
     */
    public function verifyContact(string $type, string $contact, ?string $code = null): ResponseInterface
    {
        $data = ['type' => $type];

        if ($type === 'phone') {
            $data['phone'] = $contact;
        } elseif ($type === 'email') {
            $data['email'] = $contact;
        }

        if ($code) {
            $data['code'] = $code;
        }

        $response = $this->sendRequest('POST', 'v1/ping/verify', $data);

        return $this->handleResponse($response, "Failed to send OTP to {$type}", compact('contact', 'type'));
    }

    /**
     * @throws Exception
     */
    public function sendSms(string $message, string $phone): ResponseInterface
    {
        $response = $this->sendRequest('POST', 'v1/ping/message', [
            'message' => $message,
            'destination' => [$phone],
        ]);

        return $this->handleResponse($response, 'Failed to send SMS', compact('phone', 'message'));
    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $method, string $uri, array $data): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($method, $this->apiUrl . $uri);
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('X-client-id', $this->clientId);
        $request = $request->withHeader('X-secret-id', $this->secretId);
        $request = $request->withBody($this->streamFactory->createStream(json_encode($data)));

        return $this->client->sendRequest($request);
    }

    /**
     * @throws Exception
     */
    private function handleResponse(ResponseInterface $response, string $errorMessage, array $context = []): ResponseInterface
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return $response;
        }

        $this->logError($errorMessage, array_merge($context, [
            'status' => $response->getStatusCode(),
            'response' => (string) $response->getBody(),
        ]));

        throw new Exception("{$errorMessage}. Status: {$response->getStatusCode()}");
    }
}
