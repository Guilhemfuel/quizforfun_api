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
        $questions = array(
            'Qu est ce qui est jaune et qui attend ?',
            'Qu est ce qui est blanc et qui monte aux arbres ?',
            'En quelle année les diplodocus sont revenues sur terre',
            'Pourquoi vous jouez à QuizForFun ?',
            'Quelle est la différence entre une roue de voiture et un ordinateur ?',
            'Pourquoi les lampadaires n ont pas de chaussures ?',
        );

        $reponses = array(
            array('Philippe', 'Jonathan', 'Celestin'),
            array('Un vélo', 'Un tube de dentifrice', 'Un réfrigérateur'),
            array('1865', '2052', 'Jamais', '3087'),
            array('Parce que c est drôle', 'Parce que c est vraiment drôle', 'Parce que c est drôle', 'C est mon jeu préféré'),
            array('Y en a un qui peut rouler', 'Aucune, les 2 sont des céphalopodes'),
            array('Parce qu ils traversent rarement la route', 'Parce qu ils n ont pas de pieds')
        );

        $goodAnswer = array(
            array(false, true, false),
            array(false, false, true),
            array(false, false, false, true),
            array(true, false, false, false),
            array(false, true),
            array(true, false)
        );

        foreach ($questions as $key => $q) {
            $question = new Question;
            $question->setQuestion($q);

            foreach($reponses[$key] as $k => $reponse) {
                $answer = new Answer;
                $answer->setAnswer($reponse);
                $answer->setGoodAnswer($goodAnswer[$key][$k]);
                $question->addAnswer($answer);

                $manager->persist($answer);
            }

            $manager->persist($question);
        }

        $manager->flush();
    }
}