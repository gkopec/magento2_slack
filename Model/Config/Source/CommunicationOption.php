<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CommunicationOption implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'oauth', 'label' => __('Bot User OAuth Token')],
            ['value' => 'webhook', 'label' => __('Incoming Webhook')],
        ];
    }
}
