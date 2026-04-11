<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Fixtures;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class QuizFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $quiz = new Quiz(
            Uuid::v4()->toRfc4122(),
            'Antibody Basics',
            'Questions about antibodies and the immune system',
            'Biology',
        );

        $manager->persist($quiz);

        $q1 = new Question(Uuid::v4()->toRfc4122(), 'What are antibodies?', $quiz, new ArrayCollection());
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Proteins of the immune system', true));
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Bacteria', false));
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Hormones', false));
        $manager->persist($q1);

        $q2 = new Question(Uuid::v4()->toRfc4122(), 'Which cells produce antibodies?', $quiz, new ArrayCollection());
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'B cells', true));
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'T cells', false));
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Red blood cells', false));
        $manager->persist($q2);

        $q3 = new Question(Uuid::v4()->toRfc4122(), 'What is the main function of antibodies?', $quiz, new ArrayCollection());
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Recognize and neutralize antigens', true));
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Transport oxygen', false));
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Digest food', false));
        $manager->persist($q3);

        $q4 = new Question(Uuid::v4()->toRfc4122(), 'What is an antigen?', $quiz, new ArrayCollection());
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A molecule that triggers an immune response', true));
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'An antibody', false));
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A vitamin', false));
        $manager->persist($q4);

        $q5 = new Question(Uuid::v4()->toRfc4122(), 'Which part of an antibody binds to antigens?', $quiz, new ArrayCollection());
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Variable region', true));
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Cell nucleus', false));
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Mitochondria', false));
        $manager->persist($q5);

        $q6 = new Question(Uuid::v4()->toRfc4122(), 'What happens during neutralization?', $quiz, new ArrayCollection());
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Antibodies block pathogens', true));
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Cells die immediately', false));
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Blood clots', false));
        $manager->persist($q6);

        $q7 = new Question(Uuid::v4()->toRfc4122(), 'Which antibody class is most abundant in blood?', $quiz, new ArrayCollection());
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'IgG', true));
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'IgE', false));
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'IgA', false));
        $manager->persist($q7);

        $q8 = new Question(Uuid::v4()->toRfc4122(), 'What role do antibodies play in vaccination?', $quiz, new ArrayCollection());
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'They enable faster recognition of pathogens', true));
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'They destroy all cells immediately', false));
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'They produce energy', false));
        $manager->persist($q8);

        $manager->flush();
    }
}
