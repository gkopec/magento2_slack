<?php
declare(strict_types=1);

namespace GeniusDev\Slack\Test\Unit\Model\Slack\Block;

use GeniusDev\Slack\Model\Slack\Block\Header;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    private Header $model;

    protected function setUp(): void
    {
        $this->model = new Header();
    }

    public function testCreateBasicHeader(): void
    {
        $text = 'A Heartfelt Header';
        $result = $this->model->create($text);

        $expected = [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $text
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCreateHeaderWithLevel(): void
    {
        $text = 'Level 1 Header';
        $level = 1;
        $result = $this->model->create($text, $level);

        $expected = [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
                'level' => 1
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCreateHeaderTruncatesLongText(): void
    {
        $longText = str_repeat('a', 200);
        $result = $this->model->create($longText);

        $this->assertEquals(150, mb_strlen($result['text']['text']));
        $this->assertStringEndsWith('...', $result['text']['text']);
    }
}
