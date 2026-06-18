<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Api;

interface SlackClientInterface
{
    /**
     * Send a text message to the configured Slack channel
     *
     * @param string $message
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendMessage(string $message): void;

    /**
     * Send blocks to the configured Slack channel
     *
     * @param array $blocks
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendBlocks(array $blocks): void;
}
