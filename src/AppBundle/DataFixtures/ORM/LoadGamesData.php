<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;

class LoadGamesData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $games = array(
            array('code' => 'XF5Y', 'nbPlayerMin' => 2, 'nbPlayerMax' => 10, 'description' => 'Quiz culture', 'isStarted' => 0, 'isFinished' => 0),
        );

        $players = array(
            array('name' => 'Jean', 'owner' => false, 'fingerprint' => 'fdsio88fezge54her', 'score' => '2'),
            array('name' => 'Louis', 'owner' => false, 'fingerprint' => 'apioy9846rbdfjr2', 'score' => '0'),
            array('name' => 'Risitas', 'owner' => true, 'fingerprint' => 'fzetzeyiuypdfx', 'score' => '4')
        );

        foreach($games as $key => $g) {
            $game = new Game();
            $game->setCode($g['code']);
            $game->setNbPlayerMin($g['nbPlayerMin']);
            $game->setNbPlayerMax($g['nbPlayerMax']);
            $game->setDescription($g['description']);
            $game->setIsStarted($g['isStarted']);
            $game->setIsFinished($g['isFinished']);

            foreach($players as $p) {
                $player = new Player();
                $player->setName($p['name']);
                $player->setOwner($p['owner']);
                $player->setScore($p['score']);
                $player->setFingerprint($p['fingerprint']);
                $player->setGame($game);

                $manager->persist($player);
            }

            $manager->persist($game);
        }

        $manager->flush();
    }
}