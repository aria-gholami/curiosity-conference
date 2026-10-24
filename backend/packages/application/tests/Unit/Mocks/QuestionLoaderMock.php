<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Loader;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;

final class QuestionLoaderMock implements Loader
{
    public QuestionCollection $outputQuestionCollection;

    #[\Override]
    public function load(): QuestionCollection
    {
        return $this->outputQuestionCollection;
    }
}
