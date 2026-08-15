<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoAccountResolver
{
    private const LEGACY_EXTERNAL_ID_PREFIX = 'neon:user:';

    /**
     * Resolve an SSO identity without creating a duplicate of an imported Neon user.
     *
     * @param  array<string, mixed>  $raw
     * @return array{user:?User, created:bool, claimed:bool, ambiguous:bool, match_type:string}
     */
    public function resolve(
        string $externalId,
        string $name,
        string $email,
        array $raw = [],
    ): array {
        return DB::transaction(function () use ($externalId, $name, $email, $raw) {
            $existingSsoUser = User::query()
                ->where('external_id', $externalId)
                ->lockForUpdate()
                ->first();

            if ($existingSsoUser) {
                return $this->result($existingSsoUser, matchType: 'external_id');
            }

            $existingEmailUser = User::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->lockForUpdate()
                ->first();

            if ($existingEmailUser) {
                if ($this->isUnlinkedNeonUser($existingEmailUser)) {
                    return $this->claim(
                        $existingEmailUser,
                        $externalId,
                        $name,
                        $email,
                        'email'
                    );
                }

                return $this->result($existingEmailUser, matchType: 'email');
            }

            $academicMatch = $this->findByAcademicIdentifier($email, $raw);

            if ($academicMatch['ambiguous']) {
                return $academicMatch;
            }

            if ($academicMatch['user']) {
                return $this->claim(
                    $academicMatch['user'],
                    $externalId,
                    $name,
                    $email,
                    'academic_id'
                );
            }

            $normalizedName = $this->normalizeName($name);
            $nameMatches = $this->compatibleNeonUsers($email)
                ->get()
                ->filter(fn (User $user) => $this->normalizeName($user->name) === $normalizedName)
                ->values();

            if ($nameMatches->count() > 1) {
                Log::warning('SSO: imported Neon account name is ambiguous', [
                    'sso_external_id' => $externalId,
                    'email' => $email,
                    'name' => $name,
                    'candidate_user_ids' => $nameMatches->pluck('id')->all(),
                ]);

                return $this->ambiguousResult('display_name');
            }

            if ($nameMatches->count() === 1) {
                $lockedMatch = User::query()
                    ->whereKey($nameMatches->first()->getKey())
                    ->lockForUpdate()
                    ->first();

                if ($lockedMatch && $this->isUnlinkedNeonUser($lockedMatch)) {
                    return $this->claim(
                        $lockedMatch,
                        $externalId,
                        $name,
                        $email,
                        'display_name'
                    );
                }

                return $this->ambiguousResult('display_name');
            }

            $user = User::create([
                'external_id' => $externalId,
                'name' => $name,
                'email' => $email,
                'sso_data' => [
                    'id' => $externalId,
                    'email' => $email,
                    'name' => $name,
                ],
            ]);

            return $this->result($user, created: true, matchType: 'created');
        }, 3);
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array{user:?User, created:bool, claimed:bool, ambiguous:bool, match_type:string}
     */
    private function findByAcademicIdentifier(string $email, array $raw): array
    {
        $isStudent = str_ends_with($email, '@students.undip.ac.id');
        $identifier = $isStudent
            ? ($raw['onPremisesSamAccountName'] ?? $raw['surname'] ?? explode('@', $email)[0])
            : ($raw['onPremisesSamAccountName'] ?? $raw['employeeId'] ?? explode('@', $email)[0]);
        $identifier = Str::lower(trim((string) $identifier));

        if ($identifier === '') {
            return $this->result(null, matchType: 'academic_id');
        }

        $relation = $isStudent ? 'student' : 'lecturer';
        $column = $isStudent ? 'student_number' : 'employee_number';
        $matches = $this->compatibleNeonUsers($email)
            ->whereHas($relation, fn (Builder $query) => $query
                ->whereRaw("LOWER({$column}) = ?", [$identifier]))
            ->limit(2)
            ->lockForUpdate()
            ->get();

        if ($matches->count() > 1) {
            return $this->ambiguousResult('academic_id');
        }

        return $this->result($matches->first(), matchType: 'academic_id');
    }

    private function compatibleNeonUsers(string $email): Builder
    {
        $query = User::query()
            ->where('external_id', 'like', self::LEGACY_EXTERNAL_ID_PREFIX.'%')
            ->whereNull('sso_data');

        if (str_ends_with($email, '@students.undip.ac.id')) {
            return $query->whereHas('student');
        }

        return $query->where(function (Builder $candidate) {
            $candidate
                ->whereHas('lecturer')
                ->orWhereHas('roles', fn (Builder $role) => $role
                    ->where('roles.name', 'superadmin')
                    ->where('roles.module', 'global'));
        });
    }

    /**
     * @return array{user:?User, created:bool, claimed:bool, ambiguous:bool, match_type:string}
     */
    private function claim(
        User $user,
        string $externalId,
        string $name,
        string $email,
        string $matchType,
    ): array {
        $legacyExternalId = $user->external_id;

        $user->forceFill([
            'external_id' => $externalId,
            'name' => $name,
            'email' => $email,
            'sso_data' => [
                'id' => $externalId,
                'email' => $email,
                'name' => $name,
            ],
        ])->save();
        $user->clearUserCache();

        Log::info('SSO: imported Neon account linked in place', [
            'user_id' => $user->id,
            'legacy_external_id' => $legacyExternalId,
            'sso_external_id' => $externalId,
            'email' => $email,
            'match_type' => $matchType,
        ]);

        return $this->result($user, claimed: true, matchType: $matchType);
    }

    private function isUnlinkedNeonUser(User $user): bool
    {
        return str_starts_with((string) $user->external_id, self::LEGACY_EXTERNAL_ID_PREFIX)
            && $user->sso_data === null;
    }

    private function normalizeName(string $name): string
    {
        return Str::lower(Str::squish($name));
    }

    /**
     * @return array{user:?User, created:bool, claimed:bool, ambiguous:bool, match_type:string}
     */
    private function result(
        ?User $user,
        bool $created = false,
        bool $claimed = false,
        string $matchType = '',
    ): array {
        return [
            'user' => $user,
            'created' => $created,
            'claimed' => $claimed,
            'ambiguous' => false,
            'match_type' => $matchType,
        ];
    }

    /**
     * @return array{user:null, created:false, claimed:false, ambiguous:true, match_type:string}
     */
    private function ambiguousResult(string $matchType): array
    {
        return [
            'user' => null,
            'created' => false,
            'claimed' => false,
            'ambiguous' => true,
            'match_type' => $matchType,
        ];
    }
}
