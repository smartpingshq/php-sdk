<?php

namespace Smartpings\Messaging;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class SmartpingsService extends LoggingService
{
    public const DEFAULT_API_URL = 'https://api.smartpings.com/api/';

    public function __construct(
        protected ClientInterface $client,
        protected RequestFactoryInterface $requestFactory,
        protected StreamFactoryInterface $streamFactory,
        protected string $apiUrl,
        protected string $clientId,
        protected string $secretId
    ) {}

    public static function create(
        string $clientId,
        string $secretId,
        string $apiUrl = self::DEFAULT_API_URL
    ): self {
        $client = new Client;
        $httpFactory = new HttpFactory;

        return new self(
            $client,
            $httpFactory,
            $httpFactory,
            $apiUrl,
            $clientId,
            $secretId
        );
    }

    /**
     * @throws Exception
     */
    public function verifyContact(
        string $type,
        string $contact,
        ?string $code = null,
        ?string $name = null,
        ?string $redirectUrl = null,
        ?int $expirationMinutes = null,
        ?array $promoteToListIds = null
    ): ResponseInterface {
        $data = ['type' => $type];

        if ($type === 'phone') {
            $data['phone'] = $contact;
        } elseif ($type === 'email') {
            $data['email'] = $contact;
        }

        if ($code) {
            $data['code'] = $code;
        }

        if ($name) {
            $data['name'] = $name;
        }

        if ($redirectUrl) {
            $data['redirectUrl'] = $redirectUrl;
        }

        if ($expirationMinutes !== null) {
            $data['expirationMinutes'] = $expirationMinutes;
        }

        if ($promoteToListIds !== null) {
            $data['promoteToListIds'] = $promoteToListIds;
        }

        $response = $this->sendRequest('POST', 'v1/ping/verify', $data);

        return $this->handleResponse($response, "Failed to send OTP to {$type}", compact('contact', 'type'));
    }

    /**
     * Send phone verification code
     *
     * @throws Exception
     */
    public function sendPhoneVerification(
        string $phone,
        ?string $name = null,
        ?int $expirationMinutes = null,
        ?array $promoteToListIds = null
    ): ResponseInterface {
        return $this->verifyContact(
            type: 'phone',
            contact: $phone,
            name: $name,
            expirationMinutes: $expirationMinutes,
            promoteToListIds: $promoteToListIds
        );
    }

    /**
     * Verify phone with code
     *
     * @throws Exception
     */
    public function verifyPhoneWithCode(string $phone, string $code): ResponseInterface
    {
        return $this->verifyContact(type: 'phone', contact: $phone, code: $code);
    }

    /**
     * Send email verification
     *
     * @throws Exception
     */
    public function sendEmailVerification(
        string $email,
        ?string $name = null,
        ?string $redirectUrl = null,
        ?int $expirationMinutes = null,
        ?array $promoteToListIds = null
    ): ResponseInterface {
        return $this->verifyContact(
            type: 'email',
            contact: $email,
            name: $name,
            redirectUrl: $redirectUrl,
            expirationMinutes: $expirationMinutes,
            promoteToListIds: $promoteToListIds
        );
    }

    /**
     * Verify email with code
     *
     * @throws Exception
     */
    public function verifyEmailWithCode(string $email, string $code): ResponseInterface
    {
        return $this->verifyContact(type: 'email', contact: $email, code: $code);
    }

    /**
     * @throws Exception
     */
    public function sendSms(string $message, string|array $phones): ResponseInterface
    {
        $phones = is_array($phones) ? $phones : [$phones];

        $response = $this->sendRequest('POST', 'v1/ping/message', [
            'message' => $message,
            'destination' => $phones,
        ]);

        return $this->handleResponse($response, 'Failed to send SMS', ['phones' => $phones, 'message' => $message]);
    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $method, string $uri, array $data): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($method, $this->apiUrl.$uri);
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
