<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model\Slack;

use GeniusDev\Slack\Model\Config;
use GeniusDev\Slack\Model\Slack\OAuthClient;
use JoliCode\Slack\Client;
use JoliCode\Slack\ClientFactory;
use JoliCode\Slack\Exception\SlackErrorResponse;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OAuthClientTest extends TestCase
{
    private OAuthClient $model;
    private Config|MockObject $configMock;
    private ClientFactory|MockObject $clientFactoryMock;
    private Client|MockObject $slackClientMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->clientFactoryMock = $this->createMock(ClientFactory::class);
        $this->slackClientMock = $this->createMock(Client::class);

        $this->model = new OAuthClient(
            $this->configMock,
            $this->clientFactoryMock
        );
    }

    public function testSendMessageSuccess(): void
    {
        $token = 'xoxb-test-token';
        $channel = 'C12345';
        $message = 'Test message';

        $this->configMock->expects($this->once())
            ->method('getToken')
            ->willReturn($token);
        $this->configMock->expects($this->once())
            ->method('getChannel')
            ->willReturn($channel);

        $this->clientFactoryMock->expects($this->once())
            ->method('create')
            ->with($token)
            ->willReturn($this->slackClientMock);

        $this->slackClientMock->expects($this->once())
            ->method('__call')
            ->with('chatPostMessage', [[
                'channel' => $channel,
                'text' => $message,
            ]]);

        $this->model->sendMessage($message);
    }

    public function testSendMessageThrowsExceptionIfTokenMissing(): void
    {
        $this->configMock->expects($this->once())
            ->method('getToken')
            ->willReturn('');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Slack Bot User OAuth Token is not configured.');

        $this->model->sendMessage('message');
    }

    public function testSendMessageThrowsExceptionIfChannelMissing(): void
    {
        $this->configMock->expects($this->once())
            ->method('getToken')
            ->willReturn('token');
        $this->configMock->expects($this->once())
            ->method('getChannel')
            ->willReturn('');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Slack channel is not configured.');

        $this->model->sendMessage('message');
    }

    public function testSendMessageThrowsExceptionOnSlackError(): void
    {
        $channel = 'channel';
        $message = 'message';
        $this->configMock->method('getToken')->willReturn('token');
        $this->configMock->method('getChannel')->willReturn($channel);
        $this->clientFactoryMock->method('create')->willReturn($this->slackClientMock);

        $exception = new SlackErrorResponse('Slack error', 400);

        $this->slackClientMock->expects($this->once())
            ->method('__call')
            ->with('chatPostMessage', [[
                'channel' => $channel,
                'text' => $message,
            ]])
            ->willThrowException($exception);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Failed to send Slack message via OAuth: Slack error');

        $this->model->sendMessage($message);
    }
}
