<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Sulu\Page\Domain\Model\PageInterface;
use Sulu\Page\Domain\Repository\PageRepositoryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class TriviaFixtures extends AbstractFixture
{
    private const string TRIVIA_TITLE = 'Trivia';
    private const string TRIVIA_PATH = '/trivia';
    private const string TRIVIA_TEMPLATE = 'trivia';

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
        parent::__construct($this->messageBus);
    }

    /**
     * @throws ExceptionInterface
     */
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $website = $this->pageRepository->findOneBy([
            'parentId' => null,
        ]);
        if (!$website instanceof PageInterface) {
            return;
        }

        $triviaDataEn = $this->createTriviaData(self::LOCALE_EN);
        $triviaDataDe = $this->createTriviaData(self::LOCALE_DE);
        $envelope = $this->createPage($website, $triviaDataEn);

        /** @var HandledStamp|null $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);
        /** @var PageInterface|null $trivia */
        $trivia = $handledStamp?->getResult();
        if (!$trivia instanceof PageInterface) {
            return;
        }

        $this->modifyPage($trivia, $triviaDataDe);
        $this->publishPage(
            $trivia->getUuid(),
            self::LOCALE_EN,
        );
        $this->publishPage(
            $trivia->getUuid(),
            self::LOCALE_DE,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function createTriviaData(string $locale): array
    {
        return [
            'title' => self::TRIVIA_TITLE,
            'template' => self::TRIVIA_TEMPLATE,
            'locale' => $locale,
            'url' => self::TRIVIA_PATH,
            'quizzes' => $this->createQuizzes(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function createQuizzes(): array
    {
        return [
            [
                'type' => 'quiz',
                'quizTitle' => 'Genetics Basics',
                'description' => '',
                'questions' => [
                    $this->createQuestion(
                        'What is DNA?',
                        [
                            ['Genetic material of cells', true],
                            ['A protein', false],
                            ['A type of sugar', false],
                            ['A mineral', false],
                        ],
                    ),
                    $this->createQuestion(
                        'Where is DNA located in the cell?',
                        [
                            ['Nucleus', true],
                            ['Cytoplasm only', false],
                            ['Cell membrane', false],
                            ['Golgi apparatus only', false],
                        ],
                    ),
                    $this->createQuestion(
                        'What is a gene?',
                        [
                            ['A segment of DNA', true],
                            ['A protein', false],
                            ['A cell', false],
                            ['A chromosome membrane', false],
                        ],
                    ),
                    $this->createQuestion(
                        'What is the shape of DNA?',
                        [
                            ['Double helix', true],
                            ['Single strand', false],
                            ['Circular protein', false],
                            ['Triple helix', false],
                        ],
                    ),
                    $this->createQuestion(
                        'What does DNA code for?',
                        [
                            ['Proteins', true],
                            ['Lipids', false],
                            ['Minerals', false],
                            ['Water molecules', false],
                        ],
                    ),
                    $this->createQuestion(
                        'Which bases are found in DNA?',
                        [
                            ['A, T, C, G', true],
                            ['A, U, C, G', false],
                            ['A, T, U, G', false],
                            ['A, C, U, T', false],
                        ],
                    ),
                    $this->createQuestion(
                        'What process copies DNA?',
                        [
                            ['Replication', true],
                            ['Translation', false],
                            ['Respiration', false],
                            ['Fermentation', false],
                        ],
                    ),
                    $this->createQuestion(
                        'What is RNA?',
                        [
                            ['A molecule involved in protein synthesis', true],
                            ['A type of DNA enzyme', false],
                            ['A lipid molecule', false],
                            ['A type of carbohydrate', false],
                        ],
                    ),
                ],
            ],
        ];
    }

    /**
     * @param array<int, array{0: string, 1: bool}> $answers
     *
     * @return array<string, mixed>
     */
    private function createQuestion(
        string $question,
        array $answers,
    ): array {
        return [
            'type' => 'question',
            'question' => $question,
            'answers' => \array_map(
                static fn (array $answer): array => [
                    'type' => 'answer',
                    'answer' => $answer[0],
                    'correct' => $answer[1],
                ],
                $answers,
            ),
        ];
    }
}
