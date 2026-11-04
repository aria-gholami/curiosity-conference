<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Fixtures;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class QuestionnaireFixture extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->createQuestions() as $title => $answers) {
            $question = new QuestionnaireQuestion(Uuid::v4()->toRfc4122(), $title);
            $manager->persist($question);
            foreach ($answers as $answer) {
                $manager->persist(new QuestionnaireAnswer(Uuid::v4()->toRfc4122(), $answer, $question));
            }
        }

        $manager->persist(new QuestionnaireSettings(Uuid::v4()->toRfc4122(), false));
        $manager->flush();
    }

    /**
     * @return array<string, list<string>>
     */
    private function createQuestions(): array
    {
        return [
            'What is your main field of research?' => [
                'Molecular Biology & Genetics',
                'Microbiology & Immunology',
                'Ecology & Evolution',
                'Neuroscience & Cell Biology',
            ],
            'What inspired you to become a scientist?' => [
                'Curiosity about how life works',
                'A great teacher or professor',
                'The desire to cure diseases',
                'A love for nature and animals',
            ],
            'What is your favorite part of scientific research?' => [
                'Discovering something new',
                'Working in the laboratory',
                'Analyzing data and solving problems',
                'Collaborating with other scientists',
            ],

            // Life in the Laboratory
            'What is your biggest laboratory struggle?' => [
                'Experiments that keep failing',
                'Contamination of samples',
                'Endless data analysis',
                'Equipment that suddenly stops working',
            ],
            'Which laboratory essential could you not live without?' => [
                'Microscope',
                'Pipette',
                'Computer',
                'Coffee machine',
            ],
            'How do you react when an experiment fails?' => [
                'Immediately try again',
                'Analyze every possible mistake',
                'Ask colleagues for advice',
                'Take a coffee break first',
            ],

            // Fun Biology Questions
            'If you could have any biological superpower, which would you choose?' => [
                'Regenerate limbs like an axolotl',
                'Survive extreme conditions like a tardigrade',
                'Produce energy through photosynthesis',
                'Change colors like a chameleon',
            ],
            'Which famous scientist would you most like to meet?' => [
                'Charles Darwin',
                'Rosalind Franklin',
                'Marie Curie',
                'Gregor Mendel',
            ],
            'If funding were unlimited, what would you research?' => [
                'Finding cures for major diseases',
                'Discovering unknown species',
                'Understanding the human brain',
                'Extending human lifespan',
            ],

            // Beyond Science
            'What do you enjoy most outside the laboratory?' => [
                'Sports and outdoor activities',
                'Gaming and technology',
                'Reading and creative hobbies',
                'Traveling and exploring cultures',
            ],
            'Are you an early bird or a night owl?' => [
                'Early bird - most productive before 9 AM',
                'Morning person - after my first coffee',
                'Afternoon person - peak productivity after lunch',
                'Night owl - my best ideas come after midnight',
            ],
            'If you weren\'t a scientist, what would you be?' => [
                'Doctor or healthcare professional',
                'Teacher or professor',
                'Artist or musician',
                'Entrepreneur or software developer',
            ],

            // Networking & Collaboration
            'What kind of scientific collaboration interests you most?' => [
                'Interdisciplinary research',
                'International research projects',
                'Industry and biotechnology partnerships',
                'Open-source scientific projects',
            ],
            'What would you most like to learn from another scientist?' => [
                'Advanced laboratory techniques',
                'Programming and bioinformatics',
                'Scientific writing and publishing',
                'Research funding and grant applications',
            ],
            'What are you hoping to get out of this conference?' => [
                'Meet potential collaborators',
                'Discover new research ideas',
                'Make new friends in science',
                'Learn about career opportunities',
            ],
        ];
    }
}
