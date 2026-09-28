<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Trivia;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;

interface PageModifiedDataBackendSender
{
    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    public function post(array $postData): void;
}
