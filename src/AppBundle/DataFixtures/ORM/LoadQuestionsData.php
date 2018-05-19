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
            'Les chats ont 32 muscles dans chaque oreille. Les humains en ont 6.',
            'Dans la mythologie viking, qui était la déesse Hel ?',
            'Quelle est la distance la plus proche que peuvent avoir Mars et la Terre ?',
            'En 2011, à la suite de quel événement a eu lieu l\'accident nucléaire de Fukushima ?',
            'Quel est le plus grand Etat du monde ?',
            'Que signifie "Prosélytisme" ?',
            'Quel animal appelle-t-on aussi le "Hérisson des mers" ?',
            'Quelle entreprise fait faillit en 2008, accélérant le déclenchement de la crise économique modiale ?',
            'En quelle année Christophe Colomb débarque-t-il en Amérique ?',
            'Qui joue le chauffeur vengeur dans le film "Drive" de Nicolas Winding Rifn ?'
        );

        $reponses = array(
            array('Vrai', 'Faux'),
            array('Déesse de l\'amour', 'Déesse des morts', 'Déesse de la guerre'),
            array('30 Millions de kilomètres', '55 Millions de kilomètres', '250 Millions de kilomètres', '400 millions de kilomètres'),
            array('Un séisme et un tsunami', 'La dénonciation de son mauvais état', 'Une attaque terroriste', 'Une erreur des ingénieurs'),
            array('Les Etats-Unis', 'La république démocratique du Congo', 'La Chine', 'La Russie'),
            array('Opinion contraire aux vues communément admises', 'Zéle ardent pour recruter des adeptes, pour imposer ses idées', 'Haine féroce pour toutes les innovations'),
            array('L\'oursin', 'L\'étoile de mer', 'L\'huître', 'Le crabe'),
            array('Goldman Sachs', 'Morgan Stanley', 'Barkley', 'Lehman Brothers'),
            array('1515', '2058', '1602', '1492', '1453'),
            array('Ryan Gossling', 'Norman Thavaud', 'Bradley Cooper', 'Christian Bale')
        );

        $goodAnswer = array(
            array(true, false),
            array(false, true, false),
            array(false, true, false, false),
            array(true, false, false, false),
            array(false, false, false, true),
            array(false, true, false),
            array(true, false, false, false),
            array(false, false, false, true),
            array(false, false, false, true, false),
            array(true, false, false, false)
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