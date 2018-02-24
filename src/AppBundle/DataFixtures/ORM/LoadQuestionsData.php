<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;

class LoadQuestionsData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $question = new Question();
        $question->setQuestion('Qu est ce qui est jaune et qui attend ?');

        $names = array('Philippe', 'Jonathan', 'Celestin');
        $goodAnswer = array(false, true, false);

        foreach($names as $key => $name) {
            $answer = new Answer;
            $answer->setAnswer($name);
            $answer->setGoodAnswer($goodAnswer[$key]);
            $question->addAnswer($answer);
        }

        $manager->persist($question);
        $manager->flush();
    }
}