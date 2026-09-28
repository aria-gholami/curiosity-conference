<?php

declare(strict_types=1);

namespace App\Tests\Unit\Trivia\Mocks;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use CuriosityConference\Core\Trivia\PageModifiedDataBackendSender;

final class PageModifiedDataBackendSenderMock implements PageModifiedDataBackendSender
{
    public array $inputPostData;
    public ?CouldNotSendTriviaModifiedDataException $throwCouldNotSendTriviaModifiedDataException = null;

    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    #[\Override]
    public function post(array $postData): void
    {
        $this->inputPostData = $postData;
        if ($this->throwCouldNotSendTriviaModifiedDataException instanceof CouldNotSendTriviaModifiedDataException) {
            throw $this->throwCouldNotSendTriviaModifiedDataException;
        }
    }
}
