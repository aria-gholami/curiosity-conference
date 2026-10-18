<?php

declare(strict_types=1);

namespace App\Tests\Unit\Questionnaire;

use App\Questionnaire\PageModifiedBackendSendHandler;
use App\Tests\Unit\Questionnaire\Mocks\PageModifiedDataBackendSenderMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageModifiedBackendSendHandler::class)]
final class PageModifiedBackendSendHandlerTest extends TestCase
{
    private PageModifiedDataBackendSenderMock $pageModifiedDataBackendSenderMock;
    private PageModifiedBackendSendHandler $pageModifiedBackendSendHandler;

    protected function setUp(): void
    {
        $this->pageModifiedDataBackendSenderMock = new PageModifiedDataBackendSenderMock();
        $this->pageModifiedBackendSendHandler = new PageModifiedBackendSendHandler($this->pageModifiedDataBackendSenderMock);
    }

    public function testHandleIsValid(): void
    {
        $postData = [
            [
                'type' => 'question',
                '_id' => 'e65f8b03',
                'questionTitle' => 'TestTitle',
                'answers' => [
                    [
                        'type' => 'answer',
                        '_id' => 'bf0addef',
                        'answerTitle' => '1',
                    ],
                    [
                        'type' => 'answer',
                        '_id' => '94bdfc43',
                        'answerTitle' => '2',
                    ],
                    [
                        'type' => 'answer',
                        '_id' => 'ad7c434b',
                        'answerTitle' => '3',
                    ],
                    [
                        'type' => 'answer',
                        '_id' => 'c58e3932',
                        'answerTitle' => '4',
                    ],
                ],
            ],
        ];

        $this->pageModifiedBackendSendHandler->handle($postData, false);

        $expectedInputPostData = [
            'questions' => [
                [
                    'question' => 'TestTitle',
                    'answers' => [
                        '1',
                        '2',
                        '3',
                        '4',
                    ],
                ],
            ],
            'showResults' => false,
        ];
        self::assertEquals($expectedInputPostData, $this->pageModifiedDataBackendSenderMock->inputPostData);
    }
}
