<?php

namespace Modules\Capstone\Exceptions;

/**
 * A group-membership guard failure carrying its HTTP envelope.
 *
 * The controller maps these to the same JSON status/message the inline
 * code returned. Anything abort_*() throws (HttpException family,
 * including actor resolution) keeps bubbling to the handler instead.
 */
class GroupActionException extends DomainRuleException
{
    public function __construct(
        string $message,
        private readonly int $httpCode = 400,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function httpCode(): int
    {
        return $this->httpCode;
    }
}
