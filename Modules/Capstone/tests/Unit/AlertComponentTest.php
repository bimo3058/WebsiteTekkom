<?php

namespace Modules\Capstone\Tests\Unit;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/** Render test for the library x-alert (blade-components, no prefix). */
class AlertComponentTest extends TestCase
{
    public function test_warning_style_announces_alert_with_title_and_body(): void
    {
        $html = Blade::render('<x-alert style="warning" title="Locked">Body text</x-alert>');

        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('Locked', $html);
        $this->assertStringContainsString('Body text', $html);
    }

    public function test_styles_render_distinct_markup(): void
    {
        $warning = Blade::render('<x-alert style="warning" title="W">x</x-alert>');
        $success = Blade::render('<x-alert style="success" title="S">x</x-alert>');
        $info = Blade::render('<x-alert style="info" title="I">x</x-alert>');
        $danger = Blade::render('<x-alert style="danger" title="D">x</x-alert>');

        $this->assertNotSame($warning, $success);
        $this->assertNotSame($warning, $info);
        $this->assertNotSame($warning, $danger);
    }

    public function test_role_and_extra_attributes_forward_to_root(): void
    {
        $html = Blade::render('<x-alert style="warning" role="status" x-show="finalized" x-cloak>Done</x-alert>');

        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('x-show="finalized"', $html);
        $this->assertStringContainsString('x-cloak', $html);
    }

    public function test_default_heroicon_renders_for_style(): void
    {
        $html = Blade::render('<x-alert style="warning" title="W">Body</x-alert>');

        $this->assertStringContainsString('<svg', $html);
    }
}
