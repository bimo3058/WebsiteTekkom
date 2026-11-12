<?php

namespace Modules\Capstone\Exceptions;

use Exception;

/**
 * A schedule conflict carrying the conflicting entries so the controller
 * can return the same 400 envelope the inline guards used.
 */
class ScheduleConflictException extends DomainRuleException
{
    /**
     * @param  array<int, string|array<string, mixed>>  $conflicts
     */
    public function __construct(
        string $message = 'Scheduling conflicts detected.',
        public readonly array $conflicts = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
