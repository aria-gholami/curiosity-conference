<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Fixtures;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class TriviaQuizFixture extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $triviaQuiz = new TriviaQuiz(
            Uuid::v4()->toRfc4122(),
            'Antibody Basics',
        );

        $manager->persist($triviaQuiz);

        $q1 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What are antibodies?', $triviaQuiz, new ArrayCollection());
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Proteins of the immune system', true));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Bacteria', false));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Hormones', false));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Sugars', false));
        $manager->persist($q1);

        $q2 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'Which cells produce antibodies?', $triviaQuiz, new ArrayCollection());
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'B cells', true));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'T cells', false));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Red blood cells', false));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Platelets', false));
        $manager->persist($q2);

        $q3 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is the main function of antibodies?', $triviaQuiz, new ArrayCollection());
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Recognize and neutralize antigens', true));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Transport oxygen', false));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Digest food', false));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Produce hormones', false));
        $manager->persist($q3);

        $q4 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is an antigen?', $triviaQuiz, new ArrayCollection());
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A molecule that triggers an immune response', true));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'An antibody', false));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A vitamin', false));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A red blood cell', false));
        $manager->persist($q4);

        $q5 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'Which part of an antibody binds to antigens?', $triviaQuiz, new ArrayCollection());
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Variable region', true));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Cell nucleus', false));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Mitochondria', false));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Cell membrane', false));
        $manager->persist($q5);

        $q6 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What happens during neutralization?', $triviaQuiz, new ArrayCollection());
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Antibodies block pathogens', true));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Cells die immediately', false));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Blood clots', false));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Oxygen levels increase', false));
        $manager->persist($q6);

        $q7 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'Which antibody class is most abundant in blood?', $triviaQuiz, new ArrayCollection());
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'IgG', true));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'IgE', false));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'IgA', false));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'IgD', false));
        $manager->persist($q7);

        $q8 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What role do antibodies play in vaccination?', $triviaQuiz, new ArrayCollection());
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'They enable faster recognition of pathogens', true));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'They destroy all cells immediately', false));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'They produce energy', false));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'They transport oxygen', false));
        $manager->persist($q8);

        $manager->flush();
    }
}
