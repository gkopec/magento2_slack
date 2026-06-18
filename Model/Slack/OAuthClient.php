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
        private readonly Config $config
    ) {
    }

    public function sendMessage(string $message): void
    {
        $token = $this->config->getToken();
        $channel = $this->config->getChannel();

        if (!$token) {
            throw new LocalizedException(__('Slack Bot User OAuth Token is not configured.'));
        }

        if (!$channel) {
            throw new LocalizedException(__('Slack channel is not configured.'));
        }

        $client = ClientFactory::create($token);

        try {
            $client->chatPostMessage([
                'channel' => $channel,
                'text' => $message,
            ]);
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
