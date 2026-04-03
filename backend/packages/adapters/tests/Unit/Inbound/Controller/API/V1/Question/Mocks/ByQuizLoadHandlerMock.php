<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question\Mocks;

use CuriosityConference\Application\Port\Inbound\Question\ByQuizLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Id;

final class ByQuizLoadHandlerMock implements ByQuizLoadHandler
{
    public Id $inputId;
    public QuestionCollection $outputQuestionCollection;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handle(Id $id): QuestionCollection
    {
        $this->inputId = $id;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuestionCollection;
    }
}
