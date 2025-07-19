# SmartPings PHP SDK

This is the official PHP SDK for the SmartPings API.

## Installation

You can install the package via composer:

```bash
composer require smartpings/php-sdk
```

## Getting Started

The easiest way to use the service is by using the static `create` method. This will automatically set up the required HTTP client for you.

```php
use Smartpings\Messaging\SmartpingsService;

// Instantiate the service with your credentials
$service = SmartpingsService::create(
    'your-client-id',
    'your-secret-id'
);

// Now you can use the service methods
```

## Available Methods

### Send an SMS

The `sendSms` method can send a message to a single phone number or an array of phone numbers.

```php
// Send to a single recipient
$response = $service->sendSms('Your message here', 'recipient-phone-number');

// Send to multiple recipients
$response = $service->sendSms('Your message here', [
    'recipient-1-phone-number',
    'recipient-2-phone-number',
]);

if ($response->getStatusCode() === 200) {
    echo "SMS sent successfully!";
}
```

### Verify a Contact (Phone or Email)

This method can be used to send a verification code (OTP) to a user's phone or email.

```php
// Send a verification code to a phone number
$service->verifyContact('phone', '+15551234567');

// Send a verification code to an email address
$service->verifyContact('email', 'user@example.com');

// Verify a code that the user has provided
$service->verifyContact('phone', '+15551234567', 'user-provided-code');
```

## Advanced Usage

If you need to customize the HTTP client (e.g., to add custom middleware, logging, or timeout settings), you can instantiate the `SmartpingsService` manually by passing any PSR-18 compatible client and PSR-17 compatible factories.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Smartpings\Messaging\SmartpingsService;

// 1. Create a PSR-18 HTTP Client
$client = new Client(['timeout' => 5.0]);

// 2. Create a PSR-17 Factory
$httpFactory = new HttpFactory();

// 3. Instantiate the service
$service = new SmartpingsService(
    $client,          // Your custom client
    $httpFactory,     // Request factory
    $httpFactory,     // Stream factory
    'https://api.smartpings.com/api/', // API URL
    'your-client-id',
    'your-secret-id'
);

// The service is ready to use
$response = $service->sendSms('Your message here', 'recipient-phone-number');
```

## Laravel Integration

This package includes a service provider for easy integration with Laravel.

1.  **Publish the configuration file (optional):**

    ```bash
    php artisan vendor:publish --provider="Smartpings\Messaging\SmartpingsServiceProvider"
    ```

2.  **Add your credentials to `.env`:**

    ```env
    SMARTPINGS_CLIENT_ID=your-client-id
    SMARTPINGS_SECRET_ID=your-secret-id
    ```

3.  **Use the service:**

    You can now inject the `SmartpingsService` into your controllers or services.

    ```php
    use Smartpings\Messaging\SmartpingsService;

    class YourController
    {
        public function __construct(private SmartpingsService $smartpingsService)
        {
        }

        public function sendMessage()
        {
            $response = $this->smartpingsService->sendSms('Your message here', 'recipient-phone-number');

            if ($response->getStatusCode() === 200) {
                // Message sent successfully
            }
        }
    }
    ```
