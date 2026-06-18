<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model;

use GeniusDev\Slack\Model\Config;
use GeniusDev\Slack\Model\Slack\OAuthClient;
use GeniusDev\Slack\Model\Slack\WebhookClient;
use GeniusDev\Slack\Model\SlackClientProvider;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SlackClientProviderTest extends TestCase
{
    private SlackClientProvider $model;
    private Config|MockObject $configMock;
    private OAuthClient|MockObject $oAuthClientMock;
    private WebhookClient|MockObject $webhookClientMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->oAuthClientMock = $this->createMock(OAuthClient::class);
        $this->webhookClientMock = $this->createMock(WebhookClient::class);

        $this->model = new SlackClientProvider(
            $this->configMock,
            $this->oAuthClientMock,
            $this->webhookClientMock
        );
    }

    public function testGetClientReturnsOAuthClient(): void
    {
        $this->configMock->expects($this->once())
            ->method('getCommunicationOption')
            ->willReturn('oauth');

        $this->assertSame($this->oAuthClientMock, $this->model->getClient());
    }

    public function testGetClientReturnsWebhookClient(): void
    {
        $this->configMock->expects($this->once())
            ->method('getCommunicationOption')
            ->willReturn('webhook');

        $this->assertSame($this->webhookClientMock, $this->model->getClient());
    }

    public function testGetClientThrowsExceptionForInvalidOption(): void
    {
        $this->configMock->expects($this->once())
            ->method('getCommunicationOption')
            ->willReturn('invalid');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid Slack communication option: invalid');

        $this->model->getClient();
    }
}
