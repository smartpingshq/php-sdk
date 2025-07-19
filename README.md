# SmartPings PHP SDK

This is the official PHP SDK for the SmartPings API.

## Installation

You can install the package via composer:

```bash
composer require smartpings/php-sdk
```

## Usage

### Laravel Integration

This package includes a service provider for easy integration with Laravel.

1.  **Publish the configuration file (optional):**

    ```bash
    php artisan vendor:publish --provider="Smartpings\Messaging\SmartpingsServiceProvider"
    ```

2.  **Add your credentials to `.env`:**

    ```env
    SMARTPINGS_API_URL=https://api.smartpings.com
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
            $response = $this->smartpingsService->sendSms('recipient-phone-number', 'Your message here');

            if ($response->getStatusCode() === 200) {
                // Message sent successfully
            }
        }
    }
    ```

### Framework-Agnostic Usage

You can use this package in any PHP project.

```php
use GuzzleHttp\Client;
use Smartpings\Messaging\SmartpingsService;
use Smartpings\Messaging\LoggingService; // Optional PSR-3 logger

// Create a PSR-18 HTTP client
$httpClient = new Client();

// Create a PSR-3 logger (optional)
// You can use any PSR-3 compliant logger.
$logger = new LoggingService();

// Instantiate the service
$smartpingsService = new SmartpingsService(
    $httpClient,
    'https://api.smartpings.com', // API URL
    'your-client-id',
    'your-secret-id',
    $logger // Optional
);

// Send an SMS
$response = $smartpingsService->sendSms('recipient-phone-number', 'Your message here');

// Check the response
echo $response->getStatusCode(); // e.g., 200
echo $response->getBody()->getContents(); // e.g., {"status":"success", ...}
```
