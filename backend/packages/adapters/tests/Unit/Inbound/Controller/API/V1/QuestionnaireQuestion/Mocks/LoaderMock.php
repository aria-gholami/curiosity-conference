<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion\Mocks;

use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\AnswerCollection;

final class LoaderMock implements Loader
{
    public AnswerCollection $outputAnswerCollection;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): AnswerCollection
    {
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputAnswerCollection;
    }
}
