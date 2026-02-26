<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\DeleteHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Title;

final class DeleteHandlerMock implements DeleteHandler
{
    public Title $inputTitle;
    public ?DeleteFailedException $throwException = null;

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Title $title): void
    {
        $this->inputTitle = $title;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
    }
}
