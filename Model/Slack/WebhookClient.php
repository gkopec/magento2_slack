<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model\Slack;

use GeniusDev\Slack\Api\SlackClientInterface;
use GeniusDev\Slack\Model\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;

class WebhookClient implements SlackClientInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly Curl $curl,
        private readonly Json $json
    ) {
    }

    public function sendMessage(string $message): void
    {
        $this->sendPayload(['text' => $message]);
    }

    public function sendBlocks(array $blocks): void
    {
        $this->sendPayload(['blocks' => $blocks]);
    }

    /**
     * @param array $payload
     * @return void
     * @throws LocalizedException
     */
    private function sendPayload(array $payload): void
    {
        $url = $this->config->getWebhookUrl();
        $channel = $this->config->getChannel();

        if (!$url) {
            throw new LocalizedException(__('Slack Webhook URL is not configured.'));
        }

        if ($channel) {
            $payload['channel'] = $channel;
        }

        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, $this->json->serialize($payload));

        $status = $this->curl->getStatus();
        if ($status !== 200) {
            throw new LocalizedException(
                __('Failed to send Slack message via Webhook. Status: %1, Response: %2', $status, $this->curl->getBody())
            );
        }
    }
}
