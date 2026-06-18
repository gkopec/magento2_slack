# GeniusDev Slack Integration for Magento 2

This module provides a simple way to integrate Slack notifications into your Magento 2 store. It supports both Slack Webhooks and Bot User OAuth Tokens.

## Installation

```bash
composer require geniusdev/slack
bin/magento module:enable GeniusDev_Slack
bin/magento setup:upgrade
```

## Configuration

To configure the module, go to **Stores > Configuration > GeniusDev > Slack**.

### General Configuration

*   **Communication Option**: Choose between `OAuth` or `Webhook`.
*   **Channel**: The Slack channel name (e.g., `#general`) or ID where messages should be sent.
*   **Bot User OAuth Token**: Required if `OAuth` is selected.
*   **Webhook URL**: Required if `Webhook` is selected.

## Usage

You can send Slack messages by injecting `GeniusDev\Slack\Model\SlackClientProvider` into your class.

### Example

```php
<?php
namespace YourVendor\YourModule\Model;

use GeniusDev\Slack\Model\SlackClientProvider;
use Magento\Framework\Exception\LocalizedException;

class YourClass
{
    public function __construct(
        private SlackClientProvider $slackClientProvider
    ) {}

    public function notify()
    {
        try {
            $client = $this->slackClientProvider->getClient();
            $client->sendMessage('Hello from Magento 2!');
        } catch (LocalizedException $e) {
            // Handle error (e.g., module not configured)
        }
    }
}
```

## Optional Features

### HTML to Markdown Conversion

If you want to send HTML content and have it automatically converted to Slack-compatible Markdown, you can install the `league/html-to-markdown` package:

```bash
composer require league/html-to-markdown
```

## License

OSL-3.0
