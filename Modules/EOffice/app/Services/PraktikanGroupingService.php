<?php

namespace Modules\EOffice\Services;

use Illuminate\Support\Collection;

class PraktikanGroupingService
{
    /**
     * Salin pembagian aktif menjadi draft yang dapat diedit.
     *
     * @param  Collection<int, object>  $praktikans
     * @return array{method:string,groups:array<int, array{name:string,shift:string}>,assignments:array<string, string>}
     */
    public function fromActive(Collection $praktikans, string $method = 'manual'): array
    {
        $assignments = [];
        $groups = [];

        foreach ($praktikans as $praktikan) {
            $group = trim((string) ($praktikan->kelompok ?? ''));
            if ($group === '') {
                continue;
            }

            $assignments[(string) $praktikan->id] = $group;
            $groups[$group] ??= [
                'name' => $group,
                'shift' => trim((string) ($praktikan->shift ?? '')),
            ];
        }

        return [
            'method' => in_array($method, ['urutan_sistem', 'acak', 'manual'], true) ? $method : 'manual',
            'groups' => array_values($groups),
            'assignments' => $assignments,
        ];
    }

    /**
     * @param  Collection<int, string>  $praktikanIds
     * @return array{method:string,groups:array<int, array{name:string,shift:string}>,assignments:array<string, string>}
     */
    public function automatic(Collection $praktikanIds, int $groupCount, int $shiftCount, string $method): array
    {
        $ids = $method === 'acak' ? $praktikanIds->shuffle()->values() : $praktikanIds->values();
        $draft = $this->manual($groupCount, $shiftCount);
        $draft['method'] = $method;

        $total = $ids->count();
        $baseSize = intdiv($total, $groupCount);
        $remainder = $total % $groupCount;
        $offset = 0;

        foreach ($draft['groups'] as $index => $group) {
            $size = $baseSize + ($index < $remainder ? 1 : 0);

            foreach ($ids->slice($offset, $size) as $praktikanId) {
                $draft['assignments'][(string) $praktikanId] = $group['name'];
            }

            $offset += $size;
        }

        return $draft;
    }

    /**
     * @return array{method:string,groups:array<int, array{name:string,shift:string}>,assignments:array<string, string>}
     */
    public function manual(int $groupCount, int $shiftCount): array
    {
        $groups = [];

        for ($number = 1; $number <= $groupCount; $number++) {
            $groups[] = [
                'name' => (string) $number,
                'shift' => (string) $this->shiftForGroup($number, $groupCount, $shiftCount),
            ];
        }

        return [
            'method' => 'manual',
            'groups' => $groups,
            'assignments' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $draft
     * @return array{method:string,groups:array<int, array{name:string,shift:string}>,assignments:array<string, string>}
     */
    public function normalize(?array $draft): array
    {
        $groups = collect($draft['groups'] ?? [])
            ->filter(fn ($group) => is_array($group) && trim((string) ($group['name'] ?? '')) !== '')
            ->map(fn ($group) => [
                'name' => trim((string) $group['name']),
                'shift' => trim((string) ($group['shift'] ?? '')),
            ])
            ->unique('name')
            ->values()
            ->all();

        $assignments = collect($draft['assignments'] ?? [])
            ->mapWithKeys(fn ($group, $id) => [(string) $id => trim((string) $group)])
            ->filter()
            ->all();

        return [
            'method' => in_array($draft['method'] ?? null, ['urutan_sistem', 'acak', 'manual'], true)
                ? $draft['method']
                : 'manual',
            'groups' => $groups,
            'assignments' => $assignments,
        ];
    }

    /**
     * @param  array<string, mixed>  $draft
     * @param  Collection<int, string>  $praktikanIds
     * @return array<int, string>
     */
    public function validationErrors(array $draft, Collection $praktikanIds): array
    {
        $draft = $this->normalize($draft);
        $validIds = $praktikanIds->map(fn ($id) => (string) $id)->values();
        $groupNames = collect($draft['groups'])->pluck('name');
        $errors = [];

        if ($groupNames->isEmpty()) {
            $errors[] = 'Draft belum memiliki kelompok.';
        }

        $groupsWithoutShift = collect($draft['groups'])->where('shift', '')->pluck('name');
        if ($groupsWithoutShift->isNotEmpty()) {
            $errors[] = 'Kelompok tanpa shift: '.$groupsWithoutShift->implode(', ').'.';
        }

        $unknownAssignments = collect($draft['assignments'])->keys()->diff($validIds);
        if ($unknownAssignments->isNotEmpty()) {
            $errors[] = 'Draft memuat praktikan yang tidak terdaftar pada praktikum ini.';
        }

        $invalidGroups = collect($draft['assignments'])->values()->diff($groupNames)->unique();
        if ($invalidGroups->isNotEmpty()) {
            $errors[] = 'Kelompok tidak valid pada draft: '.$invalidGroups->implode(', ').'.';
        }

        $unassignedCount = $validIds->diff(collect($draft['assignments'])->keys())->count();
        if ($unassignedCount > 0) {
            $errors[] = "{$unassignedCount} praktikan belum memiliki kelompok.";
        }

        $emptyGroups = $groupNames->filter(
            fn ($name) => ! in_array($name, $draft['assignments'], true)
        );
        if ($emptyGroups->isNotEmpty()) {
            $errors[] = 'Kelompok tanpa anggota: '.$emptyGroups->implode(', ').'.';
        }

        return $errors;
    }

    private function shiftForGroup(int $groupNumber, int $groupCount, int $shiftCount): int
    {
        return min($shiftCount, intdiv(($groupNumber - 1) * $shiftCount, $groupCount) + 1);
    }
}
