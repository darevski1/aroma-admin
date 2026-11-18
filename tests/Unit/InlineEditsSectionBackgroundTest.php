<?php

namespace Tests\Unit;

use App\Services\InlineEdits;
use PHPUnit\Framework\TestCase;

class InlineEditsSectionBackgroundTest extends TestCase
{
    public function test_section_background_image_is_stored_and_applied(): void
    {
        $svc = new InlineEdits();
        $edits = $svc->merge([], [[
            'k' => 'sty:0|section', 'n' => 0,
            'html' => json_encode(['bgimg' => '/storage/media/hero.jpg']),
        ]]);
        $this->assertCount(1, $edits);

        $out = $svc->apply('<section class="hero"><h1>Title text</h1></section>', $edits);
        $this->assertStringContainsString("background-image: url('/storage/media/hero.jpg')", $out);
        $this->assertStringContainsString('background-size: cover', $out);
    }

    public function test_unsafe_background_urls_are_rejected(): void
    {
        $svc = new InlineEdits();
        foreach (['javascript:alert(1)', "/x');background:url(//evil", 'data:image/png;base64,AAA', '//evil.com/a.jpg'] as $bad) {
            $edits = $svc->merge([], [['k' => 'sty:0|section', 'n' => 0, 'html' => json_encode(['bgimg' => $bad])]]);
            $this->assertSame([], $edits, $bad);
        }
    }
}
