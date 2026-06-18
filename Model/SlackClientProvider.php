<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model;

use GeniusDev\Slack\Api\SlackClientInterface;
use GeniusDev\Slack\Model\Slack\OAuthClient;
use GeniusDev\Slack\Model\Slack\WebhookClient;
use Magento\Framework\Exception\LocalizedException;

readonly class SlackClientProvider
{
    public function __construct(
        private Config        $config,
        private OAuthClient   $oAuthClient,
        private WebhookClient $webhookClient
    ) {
    }

    /**
     * Get the active Slack client based on configuration
     *
     * @return SlackClientInterface
     * @throws LocalizedException
     */
    public function getClient(): SlackClientInterface
    {
        $option = $this->config->getCommunicationOption();

        return match ($option) {
            'oauth' => $this->oAuthClient,
            'webhook' => $this->webhookClient,
            default => throw new LocalizedException(__('Invalid Slack communication option: %1', $option)),
        };
    }
}
