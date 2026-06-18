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
            
            // Simple text message
            $client->sendMessage('Hello from Magento 2!');
            
            // Complex message with Blocks
            $client->sendBlocks([
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '*New order received!* \n <https://example.com|View Order>'
                    ]
                ]
            ]);
        } catch (LocalizedException $e) {
            // Handle error (e.g., module not configured)
        }
    }
}
```

### Table Support

You can easily generate Slack table blocks using `GeniusDev\Slack\Model\Slack\Block\Table`.

```php
use GeniusDev\Slack\Model\Slack\Block\Table as TableBlock;

// ...

public function __construct(
    private SlackClientProvider $slackClientProvider,
    private TableBlock $tableBlock
) {}

public function sendTable()
{
    $headers = ['Order ID', 'Customer'];
    $rows = [
        ['#10001', 'John Doe'],
        ['#10002', 'Jane Smith']
    ];
    
    $table = $this->tableBlock->create($headers, $rows);
    
    $this->slackClientProvider->getClient()->sendBlocks([$table]);
}

### Header Support

You can create header blocks using `GeniusDev\Slack\Model\Slack\Block\Header`.

```php
use GeniusDev\Slack\Model\Slack\Block\Header as HeaderBlock;

// ...

public function __construct(
    private SlackClientProvider $slackClientProvider,
    private HeaderBlock $headerBlock
) {}

public function sendWithHeader()
{
    $header = $this->headerBlock->create('A Heartfelt Header');
    
    $this->slackClientProvider->getClient()->sendBlocks([$header]);
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
