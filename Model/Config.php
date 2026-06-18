<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_COMMUNICATION_OPTION = 'geniusdev_slack/general/communication_option';
    private const XML_PATH_CHANNEL = 'geniusdev_slack/general/channel';
    private const XML_PATH_TOKEN = 'geniusdev_slack/general/token';
    private const XML_PATH_WEBHOOK_URL = 'geniusdev_slack/general/webhook_url';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function getCommunicationOption(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_COMMUNICATION_OPTION, $scopeType, $scopeCode);
    }

    public function getChannel(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CHANNEL, $scopeType, $scopeCode);
    }

    public function getToken(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): string
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_TOKEN, $scopeType, $scopeCode);
        return $value ? $this->encryptor->decrypt($value) : '';
    }

    public function getWebhookUrl(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): string
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_WEBHOOK_URL, $scopeType, $scopeCode);
        return $value ? $this->encryptor->decrypt($value) : '';
    }
}
