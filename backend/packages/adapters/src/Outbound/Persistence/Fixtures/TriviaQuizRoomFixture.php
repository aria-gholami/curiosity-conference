<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Fixtures;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class TriviaQuizRoomFixture extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $triviaQuiz = new TriviaQuiz(
            Uuid::v4()->toRfc4122(),
            'Genetics Basics',
        );

        $manager->persist($triviaQuiz);

        $q1 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is DNA?', $triviaQuiz, new ArrayCollection());
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Genetic material of cells', true));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A protein', false));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A type of sugar', false));
        $q1->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A mineral', false));
        $manager->persist($q1);

        $q2 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'Where is DNA located in the cell?', $triviaQuiz, new ArrayCollection());
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Nucleus', true));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Cytoplasm only', false));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Cell membrane', false));
        $q2->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Golgi apparatus only', false));
        $manager->persist($q2);

        $q3 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is a gene?', $triviaQuiz, new ArrayCollection());
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A segment of DNA', true));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A protein', false));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A cell', false));
        $q3->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A chromosome membrane', false));
        $manager->persist($q3);

        $q4 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is the shape of DNA?', $triviaQuiz, new ArrayCollection());
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Double helix', true));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Single strand', false));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Circular protein', false));
        $q4->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Triple helix', false));
        $manager->persist($q4);

        $q5 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What does DNA code for?', $triviaQuiz, new ArrayCollection());
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Proteins', true));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Lipids', false));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Minerals', false));
        $q5->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Water molecules', false));
        $manager->persist($q5);

        $q6 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'Which bases are found in DNA?', $triviaQuiz, new ArrayCollection());
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A, T, C, G', true));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A, U, C, G', false));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A, T, U, G', false));
        $q6->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A, C, U, T', false));
        $manager->persist($q6);

        $q7 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What process copies DNA?', $triviaQuiz, new ArrayCollection());
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Replication', true));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Translation', false));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Respiration', false));
        $q7->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'Fermentation', false));
        $manager->persist($q7);

        $q8 = new TriviaQuestion(Uuid::v4()->toRfc4122(), 'What is RNA?', $triviaQuiz, new ArrayCollection());
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A molecule involved in protein synthesis', true));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A type of DNA enzyme', false));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A lipid molecule', false));
        $q8->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), 'A type of carbohydrate', false));
        $manager->persist($q8);
        $manager->persist($q8);

        $host = new User(Uuid::v4()->toRfc4122(), 'host_user');
        $player1 = new User(Uuid::v4()->toRfc4122(), 'player_one');
        $player2 = new User(Uuid::v4()->toRfc4122(), 'player_two');

        $manager->persist($host);
        $manager->persist($player1);
        $manager->persist($player2);

        $triviaQuizRoom = new TriviaQuizRoom(
            Uuid::v4()->toRfc4122(),
            'Group 1',
            $triviaQuiz,
            null,
            $host,
            TriviaQuizRoom::STATUS_CREATED,
        );
        $triviaQuizRoom->addTriviaParticipant($player1);
        $triviaQuizRoom->addTriviaParticipant($player2);
        $manager->persist($triviaQuizRoom);

        $manager->flush();
    }
}
