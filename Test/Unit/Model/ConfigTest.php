<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model;

use GeniusDev\Slack\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $model;
    private ScopeConfigInterface|MockObject $scopeConfigMock;
    private EncryptorInterface|MockObject $encryptorMock;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->encryptorMock = $this->createMock(EncryptorInterface::class);

        $this->model = new Config(
            $this->scopeConfigMock,
            $this->encryptorMock
        );
    }

    public function testGetCommunicationOption(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('geniusdev_slack/general/communication_option', ScopeInterface::SCOPE_STORE, null)
            ->willReturn('oauth');

        $this->assertEquals('oauth', $this->model->getCommunicationOption());
    }

    public function testGetChannel(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('geniusdev_slack/general/channel', ScopeInterface::SCOPE_STORE, null)
            ->willReturn('#general');

        $this->assertEquals('#general', $this->model->getChannel());
    }

    public function testGetToken(): void
    {
        $encryptedToken = 'encrypted_token';
        $decryptedToken = 'decrypted_token';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('geniusdev_slack/general/token', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($encryptedToken);

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with($encryptedToken)
            ->willReturn($decryptedToken);

        $this->assertEquals($decryptedToken, $this->model->getToken());
    }

    public function testGetTokenReturnsEmptyStringIfConfigMissing(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $this->encryptorMock->expects($this->never())
            ->method('decrypt');

        $this->assertEquals('', $this->model->getToken());
    }

    public function testGetWebhookUrl(): void
    {
        $encryptedUrl = 'encrypted_url';
        $decryptedUrl = 'https://hooks.slack.com/services/T000/B000/XXX';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('geniusdev_slack/general/webhook_url', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($encryptedUrl);

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with($encryptedUrl)
            ->willReturn($decryptedUrl);

        $this->assertEquals($decryptedUrl, $this->model->getWebhookUrl());
    }
}
