<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Model\Slack\Block;

class Table
{
    /**
     * Create a Slack table block
     *
     * @param array $headers
     * @param array $rows
     * @param array $columnSettings
     * @return array
     */
    public function create(array $headers, array $rows, array $columnSettings = []): array
    {
        $tableRows = [];

        // Add headers as the first row if provided
        if (!empty($headers)) {
            $headerRow = [];
            foreach ($headers as $header) {
                $headerRow[] = [
                    'type' => 'raw_text',
                    'text' => (string)$header
                ];
            }
            $tableRows[] = $headerRow;
        }

        // Add data rows
        foreach ($rows as $row) {
            $tableRow = [];
            foreach ($row as $cell) {
                if (is_array($cell) && isset($cell['type'])) {
                    // If it's already a Slack element (like rich_text), use it as is
                    $tableRow[] = $cell;
                } else {
                    // Default to raw_text
                    $tableRow[] = [
                        'type' => 'raw_text',
                        'text' => (string)$cell
                    ];
                }
            }
            $tableRows[] = $tableRow;
        }

        $block = [
            'type' => 'table',
            'rows' => $tableRows
        ];

        if (!empty($columnSettings)) {
            $block['column_settings'] = $columnSettings;
        }

        return $block;
    }
}
