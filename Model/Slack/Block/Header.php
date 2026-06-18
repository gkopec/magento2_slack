<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model\Slack\Block;

class Header
{
    /**
     * Create a Slack header block
     *
     * @param string $text Maximum length 150 characters.
     * @param int|null $level Custom level (1-4), though not standard Slack, it's requested.
     * @return array
     */
    public function create(string $text, ?int $level = null): array
    {
        if (mb_strlen($text) > 150) {
            $text = mb_substr($text, 0, 147) . '...';
        }

        $textObject = [
            'type' => 'plain_text',
            'text' => $text
        ];

        if ($level !== null) {
            $textObject['level'] = $level;
        }

        return [
            'type' => 'header',
            'text' => $textObject
        ];
    }
}
