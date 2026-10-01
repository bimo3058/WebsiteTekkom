<?php

namespace Modules\Capstone\Tests\Unit;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/** Render test for the extended x-capstone::alert variants. */
class AlertComponentTest extends TestCase
{
    public function test_warning_variant_uses_amber_scale_and_alert_role(): void
    {
        $html = Blade::render('<x-capstone::alert variant="warning" title="Locked">Body text</x-capstone::alert>');

        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('border-amber-500', $html);
        $this->assertStringContainsString('bg-amber-50', $html);
        $this->assertStringContainsString('text-amber-700', $html);
        $this->assertStringContainsString('<h3', $html);
        $this->assertStringContainsString('Locked', $html);
        $this->assertStringContainsString('Body text', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    public function test_success_and_info_variants_use_their_scales(): void
    {
        $success = Blade::render('<x-capstone::alert variant="success" title="Done">Ok</x-capstone::alert>');
        $this->assertStringContainsString('border-emerald-500', $success);
        $this->assertStringContainsString('bg-emerald-50', $success);

        $info = Blade::render('<x-capstone::alert variant="info" title="Note">Hi</x-capstone::alert>');
        $this->assertStringContainsString('border-sky-500', $info);
        $this->assertStringContainsString('bg-sky-50', $info);
    }

    public function test_destructive_variant_keeps_original_styling(): void
    {
        $html = Blade::render('<x-capstone::alert variant="destructive" title="Blocked">No</x-capstone::alert>');

        $this->assertStringContainsString('text-destructive', $html);
        $this->assertStringContainsString('border-destructive/50', $html);
    }

    public function test_role_and_extra_attributes_forward_to_root(): void
    {
        $html = Blade::render('<x-capstone::alert variant="warning" role="status" x-show="finalized" x-cloak>Done</x-capstone::alert>');

        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('x-show="finalized"', $html);
        $this->assertStringContainsString('x-cloak', $html);
        // No title: no h3 emitted.
        $this->assertStringNotContainsString('<h3', $html);
    }

    public function test_explicit_icon_wins_over_variant_default(): void
    {
        $html = Blade::render('<x-capstone::alert variant="warning" icon="UserCheck">Body</x-capstone::alert>');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('text-amber-600', $html);
    }
}
