<?php

namespace Tests\Unit;

use Modules\Capstone\Models\Group;
use PHPUnit\Framework\TestCase;

class CapstoneGroupDisplayNameTest extends TestCase
{
    public function test_name_falls_back_to_code_without_throwing(): void
    {
        $group = new Group(['code' => 'GRP-001']);

        $this->assertSame('GRP-001', $group->name);
    }

    public function test_name_returns_null_when_no_code_present(): void
    {
        $group = new Group;

        $this->assertNull($group->name);
        $this->assertSame('Group 7', $group->name ?? $group->code ?? 'Group 7');
    }

    public function test_explicit_name_attribute_is_preferred(): void
    {
        $group = new Group;
        $group->setRawAttributes(['name' => 'My Team', 'code' => 'GRP-001']);

        $this->assertSame('My Team', $group->name);
    }
}
