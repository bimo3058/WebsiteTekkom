<?php

namespace Modules\Capstone\Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Modules\Capstone\Http\Requests\Scheduling\AvailableLocationsRequest;
use Modules\Capstone\Http\Requests\Scheduling\CalendarIndexRequest;
use Modules\Capstone\Http\Requests\Scheduling\SeminarScheduleRequest;
use Modules\Capstone\Http\Requests\Scheduling\StoreLocationRequest;
use Modules\Capstone\Http\Requests\Scheduling\StoreScheduleRequest;
use Modules\Capstone\Http\Requests\Scheduling\UpdateLocationRequest;
use Modules\Capstone\Http\Requests\Scheduling\UpdateScheduleRequest;
use Tests\TestCase;

/** Pure unit test: scheduling request contracts mirror the former inline rules. No database needed. */
class SchedulingRequestContractsTest extends TestCase
{
    private function userWithRole(string $role): User
    {
        $user = new User(['name' => 'Test', 'email' => 'test@example.test']);
        $user->setRelation('roles', collect([new Role(['name' => $role])]));

        return $user;
    }

    public function test_store_location_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'name' => 'required|string|max:255|unique:capstone_locations,name',
                'capacity' => 'nullable|integer|min:1',
                'type' => 'required|string|in:online',
                'description' => 'nullable|string|max:1000',
                'eoffice_ruangan_id' => 'prohibited',
            ],
            (new StoreLocationRequest)->rules()
        );
    }

    public function test_update_location_rules_match_inline_contract(): void
    {
        $rules = (new UpdateLocationRequest)->rules();

        $this->assertStringStartsWith('sometimes|string|max:255|unique:capstone_locations,name,', $rules['name']);
        $this->assertSame('nullable|integer|min:1', $rules['capacity']);
        $this->assertSame('prohibited', $rules['eoffice_ruangan_id']);
    }

    public function test_available_locations_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'exclude_schedule_id' => 'nullable|integer',
                'exclude_seminar_id' => 'nullable|integer',
                'exclude_ta_defense_id' => 'nullable|integer',
                'exclude_expo_id' => 'nullable|integer',
            ],
            (new AvailableLocationsRequest)->rules()
        );
    }

    public function test_store_schedule_admin_rules_match_inline_contract(): void
    {
        $this->be($this->userWithRole('admin_capstone'));

        $this->assertSame(
            [
                'group_id' => 'required|exists:capstone_groups,id',
                'type' => ['required', 'string', 'in:SEMPRO,SIDANG,EXPO'],
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i,H:i:s',
                'end_time' => 'required|date_format:H:i,H:i:s|after:start_time',
                'room' => 'required|string',
                'mode' => 'nullable|string|in:online,offline',
                'notes' => 'nullable|string|max:1000',
            ],
            (new StoreScheduleRequest)->rules()
        );
    }

    public function test_store_schedule_dosen_branch_matches_inline_contract(): void
    {
        $this->be($this->userWithRole('dosen'));

        $rules = (new StoreScheduleRequest)->rules();

        $this->assertSame(['required', 'string', 'in:BIMBINGAN'], $rules['type']);
        $this->assertSame('required|exists:capstone_groups,id', $rules['group_id']);
    }

    public function test_update_schedule_admin_rules_match_inline_contract(): void
    {
        $this->be($this->userWithRole('admin_capstone'));

        $this->assertSame(
            [
                'group_id' => 'exists:capstone_groups,id',
                'type' => ['string', 'in:SEMPRO,SIDANG,EXPO'],
                'date' => 'date',
                'start_time' => 'required_with:end_time|date_format:H:i,H:i:s',
                'end_time' => 'required_with:start_time|date_format:H:i,H:i:s|after:start_time',
                'room' => 'string',
                'mode' => 'nullable|string|in:online,offline',
                'notes' => 'nullable|string|max:1000',
            ],
            (new UpdateScheduleRequest)->rules()
        );
    }

    public function test_seminar_schedule_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'date' => 'required|date|after:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'room' => 'nullable|string|max:100',
                'examiner_1_id' => 'required|exists:lecturers,id',
                'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
            ],
            (new SeminarScheduleRequest)->rules()
        );
    }

    public function test_calendar_index_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => 'nullable|integer|min:1'],
            (new CalendarIndexRequest)->rules()
        );
    }
}
