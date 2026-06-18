<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model\Slack\Block;

use GeniusDev\Slack\Model\Slack\Block\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    private Table $model;

    protected function setUp(): void
    {
        $this->model = new Table();
    }

    public function testCreateSimpleTable(): void
    {
        $headers = ['Header A', 'Header B'];
        $rows = [
            ['Data 1A', 'Data 1B'],
            ['Data 2A', 'Data 2B']
        ];

        $result = $this->model->create($headers, $rows);

        $expected = [
            'type' => 'table',
            'rows' => [
                [
                    ['type' => 'raw_text', 'text' => 'Header A'],
                    ['type' => 'raw_text', 'text' => 'Header B']
                ],
                [
                    ['type' => 'raw_text', 'text' => 'Data 1A'],
                    ['type' => 'raw_text', 'text' => 'Data 1B']
                ],
                [
                    ['type' => 'raw_text', 'text' => 'Data 2A'],
                    ['type' => 'raw_text', 'text' => 'Data 2B']
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCreateTableWithColumnSettings(): void
    {
        $headers = ['Header A', 'Header B'];
        $rows = [['Data 1A', 'Data 1B']];
        $columnSettings = [
            ['is_wrapped' => true],
            ['align' => 'right']
        ];

        $result = $this->model->create($headers, $rows, $columnSettings);

        $this->assertArrayHasKey('column_settings', $result);
        $this->assertEquals($columnSettings, $result['column_settings']);
    }

    public function testCreateTableWithRichText(): void
    {
        $headers = ['Header A', 'Header B'];
        $richTextCell = [
            'type' => 'rich_text',
            'elements' => [
                [
                    'type' => 'rich_text_section',
                    'elements' => [
                        [
                            'text' => 'Link',
                            'type' => 'link',
                            'url' => 'https://slack.com'
                        ]
                    ]
                ]
            ]
        ];
        $rows = [
            ['Data 1A', $richTextCell]
        ];

        $result = $this->model->create($headers, $rows);

        $this->assertEquals($richTextCell, $result['rows'][1][1]);
    }
}
