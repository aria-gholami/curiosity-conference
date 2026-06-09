<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Fixtures;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
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
final class QuizRoomFixture extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $quiz = new Quiz(
            Uuid::v4()->toRfc4122(),
            'Genetics Basics',
        );

        $manager->persist($quiz);

        $q1 = new Question(Uuid::v4()->toRfc4122(), 'What is DNA?', $quiz, new ArrayCollection());
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Genetic material of cells', true));
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A protein', false));
        $q1->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A type of sugar', false));
        $manager->persist($q1);

        $q2 = new Question(Uuid::v4()->toRfc4122(), 'Where is DNA located in the cell?', $quiz, new ArrayCollection());
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Nucleus', true));
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Cytoplasm only', false));
        $q2->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Cell membrane', false));
        $manager->persist($q2);

        $q3 = new Question(Uuid::v4()->toRfc4122(), 'What is a gene?', $quiz, new ArrayCollection());
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A segment of DNA', true));
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A protein', false));
        $q3->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A cell', false));
        $manager->persist($q3);

        $q4 = new Question(Uuid::v4()->toRfc4122(), 'What is the shape of DNA?', $quiz, new ArrayCollection());
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Double helix', true));
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Single strand', false));
        $q4->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Circular protein', false));
        $manager->persist($q4);

        $q5 = new Question(Uuid::v4()->toRfc4122(), 'What does DNA code for?', $quiz, new ArrayCollection());
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Proteins', true));
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Lipids', false));
        $q5->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Minerals', false));
        $manager->persist($q5);

        $q6 = new Question(Uuid::v4()->toRfc4122(), 'Which bases are found in DNA?', $quiz, new ArrayCollection());
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A, T, C, G', true));
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A, U, C, G', false));
        $q6->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A, T, U, G', false));
        $manager->persist($q6);

        $q7 = new Question(Uuid::v4()->toRfc4122(), 'What process copies DNA?', $quiz, new ArrayCollection());
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Replication', true));
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Translation', false));
        $q7->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'Respiration', false));
        $manager->persist($q7);

        $q8 = new Question(Uuid::v4()->toRfc4122(), 'What is RNA?', $quiz, new ArrayCollection());
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A molecule involved in protein synthesis', true));
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A type of DNA enzyme', false));
        $q8->addAnswer(new Answer(Uuid::v4()->toRfc4122(), 'A lipid molecule', false));
        $manager->persist($q8);

        $host = new User(Uuid::v4()->toRfc4122(), 'host_user');
        $player1 = new User(Uuid::v4()->toRfc4122(), 'player_one');
        $player2 = new User(Uuid::v4()->toRfc4122(), 'player_two');

        $manager->persist($host);
        $manager->persist($player1);
        $manager->persist($player2);

        $room = new QuizRoom(
            Uuid::v4()->toRfc4122(),
            'Group 1',
            $quiz,
            null,
            $host,
            QuizRoom::STATUS_CREATED,
        );
        $room->addParticipant($player1);
        $room->addParticipant($player2);
        $manager->persist($room);

        $manager->flush();
    }
}
