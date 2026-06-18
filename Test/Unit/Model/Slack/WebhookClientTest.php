<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model\Slack;

use GeniusDev\Slack\Model\Config;
use GeniusDev\Slack\Model\Slack\WebhookClient;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebhookClientTest extends TestCase
{
    private WebhookClient $model;
    private Config|MockObject $configMock;
    private Curl|MockObject $curlMock;
    private Json|MockObject $jsonMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->curlMock = $this->createMock(Curl::class);
        $this->jsonMock = $this->createMock(Json::class);

        $this->model = new WebhookClient(
            $this->configMock,
            $this->curlMock,
            $this->jsonMock
        );
    }

    public function testSendMessageSuccess(): void
    {
        $url = 'https://hooks.slack.com/services/T000/B000/XXX';
        $channel = '#general';
        $message = 'Test message';
        $payload = ['text' => $message, 'channel' => $channel];
        $serializedPayload = '{"text":"Test message","channel":"#general"}';

        $this->configMock->expects($this->once())
            ->method('getWebhookUrl')
            ->willReturn($url);
        $this->configMock->expects($this->once())
            ->method('getChannel')
            ->willReturn($channel);

        $this->jsonMock->expects($this->once())
            ->method('serialize')
            ->with($payload)
            ->willReturn($serializedPayload);

        $this->curlMock->expects($this->once())
            ->method('addHeader')
            ->with('Content-Type', 'application/json');
        $this->curlMock->expects($this->once())
            ->method('post')
            ->with($url, $serializedPayload);
        $this->curlMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(200);

        $this->model->sendMessage($message);
    }

    public function testSendMessageThrowsExceptionIfUrlMissing(): void
    {
        $this->configMock->expects($this->once())
            ->method('getWebhookUrl')
            ->willReturn('');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Slack Webhook URL is not configured.');

        $this->model->sendMessage('message');
    }

    public function testSendMessageThrowsExceptionOnFailureStatus(): void
    {
        $url = 'https://hooks.slack.com/services/T000/B000/XXX';
        $this->configMock->method('getWebhookUrl')->willReturn($url);

        $this->curlMock->method('getStatus')->willReturn(404);
        $this->curlMock->method('getBody')->willReturn('Not Found');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Failed to send Slack message via Webhook. Status: 404, Response: Not Found');

        $this->model->sendMessage('message');
    }
}
