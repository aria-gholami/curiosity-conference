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
final class QuestionnaireFixtures extends AbstractFixture
{
    private const string QUESTIONNAIRE_TITLE = 'Questionnaire';
    private const string QUESTIONNAIRE_PATH = '/questionnaire';
    private const string QUESTIONNAIRE_TEMPLATE = 'questionnaire';

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

        $questionnaireDataEn = $this->createQuestionnaireData(self::LOCALE_EN);
        $questionnaireDataDe = $this->createQuestionnaireData(self::LOCALE_DE);
        $envelope = $this->createPage($website, $questionnaireDataEn);
        /** @var HandledStamp|null $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);
        /** @var PageInterface|null $questionnaire */
        $questionnaire = $handledStamp?->getResult();
        if (!$questionnaire instanceof PageInterface) {
            return;
        }

        $this->modifyPage($questionnaire, $questionnaireDataDe);
        $this->publishPage(
            $questionnaire->getUuid(),
            self::LOCALE_EN,
        );
        $this->publishPage(
            $questionnaire->getUuid(),
            self::LOCALE_DE,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function createQuestionnaireData(string $locale): array
    {
        return [
            'title' => self::QUESTIONNAIRE_TITLE,
            'template' => self::QUESTIONNAIRE_TEMPLATE,
            'locale' => $locale,
            'url' => self::QUESTIONNAIRE_PATH,
            'questions' => $this->createQuestions(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function createQuestions(): array
    {
        return [
            $this->createQuestion(
                'What is your main field of research?',
                [
                    'Molecular Biology & Genetics',
                    'Microbiology & Immunology',
                    'Ecology & Evolution',
                    'Neuroscience & Cell Biology',
                ],
            ),
            $this->createQuestion(
                'What inspired you to become a scientist?',
                [
                    'Curiosity about how life works',
                    'A great teacher or professor',
                    'The desire to cure diseases',
                    'A love for nature and animals',
                ],
            ),
            $this->createQuestion(
                'What is your favorite part of scientific research?',
                [
                    'Discovering something new',
                    'Working in the laboratory',
                    'Analyzing data and solving problems',
                    'Collaborating with other scientists',
                ],
            ),

            // Life in the Laboratory
            $this->createQuestion(
                'What is your biggest laboratory struggle?',
                [
                    'Experiments that keep failing',
                    'Contamination of samples',
                    'Endless data analysis',
                    'Equipment that suddenly stops working',
                ],
            ),
            $this->createQuestion(
                'Which laboratory essential could you not live without?',
                [
                    'Microscope',
                    'Pipette',
                    'Computer',
                    'Coffee machine',
                ],
            ),
            $this->createQuestion(
                'How do you react when an experiment fails?',
                [
                    'Immediately try again',
                    'Analyze every possible mistake',
                    'Ask colleagues for advice',
                    'Take a coffee break first',
                ],
            ),

            // Fun Biology Questions
            $this->createQuestion(
                'If you could have any biological superpower, which would you choose?',
                [
                    'Regenerate limbs like an axolotl',
                    'Survive extreme conditions like a tardigrade',
                    'Produce energy through photosynthesis',
                    'Change colors like a chameleon',
                ],
            ),
            $this->createQuestion(
                'Which famous scientist would you most like to meet?',
                [
                    'Charles Darwin',
                    'Rosalind Franklin',
                    'Marie Curie',
                    'Gregor Mendel',
                ],
            ),
            $this->createQuestion(
                'If funding were unlimited, what would you research?',
                [
                    'Finding cures for major diseases',
                    'Discovering unknown species',
                    'Understanding the human brain',
                    'Extending human lifespan',
                ],
            ),

            // Beyond Science
            $this->createQuestion(
                'What do you enjoy most outside the laboratory?',
                [
                    'Sports and outdoor activities',
                    'Gaming and technology',
                    'Reading and creative hobbies',
                    'Traveling and exploring cultures',
                ],
            ),
            $this->createQuestion(
                'Are you an early bird or a night owl?',
                [
                    'Early bird - most productive before 9 AM',
                    'Morning person - after my first coffee',
                    'Afternoon person - peak productivity after lunch',
                    'Night owl - my best ideas come after midnight',
                ],
            ),
            $this->createQuestion(
                'If you weren\'t a scientist, what would you be?',
                [
                    'Doctor or healthcare professional',
                    'Teacher or professor',
                    'Artist or musician',
                    'Entrepreneur or software developer',
                ],
            ),

            // Networking & Collaboration
            $this->createQuestion(
                'What kind of scientific collaboration interests you most?',
                [
                    'Interdisciplinary research',
                    'International research projects',
                    'Industry and biotechnology partnerships',
                    'Open-source scientific projects',
                ],
            ),
            $this->createQuestion(
                'What would you most like to learn from another scientist?',
                [
                    'Advanced laboratory techniques',
                    'Programming and bioinformatics',
                    'Scientific writing and publishing',
                    'Research funding and grant applications',
                ],
            ),
            $this->createQuestion(
                'What are you hoping to get out of this conference?',
                [
                    'Meet potential collaborators',
                    'Discover new research ideas',
                    'Make new friends in science',
                    'Learn about career opportunities',
                ],
            ),
        ];
    }

    /**
     * @param array<int, string> $answers
     *
     * @return array<string, mixed>
     */
    private function createQuestion(
        string $questionTitle,
        array $answers,
    ): array {
        return [
            'type' => 'question',
            'questionTitle' => $questionTitle,
            'answers' => \array_map(
                static fn (string $answer): array => [
                    'type' => 'answer',
                    'answerTitle' => $answer,
                ],
                $answers,
            ),
        ];
    }
}
