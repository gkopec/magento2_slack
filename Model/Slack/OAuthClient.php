<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model\Slack;

use GeniusDev\Slack\Api\SlackClientInterface;
use GeniusDev\Slack\Model\Config;
use JoliCode\Slack\ClientFactory;
use JoliCode\Slack\Exception\SlackErrorResponse;
use Magento\Framework\Exception\LocalizedException;

class OAuthClient implements SlackClientInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ClientFactory $clientFactory
    ) {
    }

    public function sendMessage(string $message): void
    {
        $this->sendPayload(['text' => $message]);
    }

    public function sendBlocks(array $blocks): void
    {
        $this->sendPayload(['blocks' => json_encode($blocks)]);
    }

    /**
     * @param array $payload
     * @return void
     * @throws LocalizedException
     */
    private function sendPayload(array $payload): void
    {
        $token = $this->config->getToken();
        $channel = $this->config->getChannel();

        if (!$token) {
            throw new LocalizedException(__('Slack Bot User OAuth Token is not configured.'));
        }

        if (!$channel) {
            throw new LocalizedException(__('Slack channel is not configured.'));
        }

        $payload['channel'] = $channel;

        $client = $this->clientFactory->create($token);

        try {
            $client->chatPostMessage($payload);
        } catch (SlackErrorResponse $e) {
            throw new LocalizedException(
                __('Failed to send Slack message via OAuth: %1', $e->getMessage())
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('An error occurred while sending Slack message via OAuth: %1', $e->getMessage())
            );
        }
    }
}
