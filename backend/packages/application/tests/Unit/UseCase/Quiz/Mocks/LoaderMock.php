<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Application\Port\Outbound\Quiz\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizCollection;

final class LoaderMock implements Loader
{
    public QuizCollection $outputQuizCollection;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): QuizCollection
    {
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuizCollection;
    }
}
