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
            array('code' => 'XF5Y', 'nbPlayerMin' => 2, 'nbPlayerMax' => 10, 'description' => 'Quiz culture'),
        );

        $players = array(
            array('name' => 'Jean', 'device' => 'android7854', 'owner' => false, 'score' => '2'),
            array('name' => 'Louis', 'device' => 'ios8796', 'owner' => false, 'score' => '0'),
            array('name' => 'Risitas', 'device' => 'android9843', 'owner' => true, 'score' => '4')
        );

        foreach($games as $key => $g) {
            $game = new Game();
            $game->setCode($g['code']);
            $game->setNbPlayerMin($g['nbPlayerMin']);
            $game->setNbPlayerMax($g['nbPlayerMax']);
            $game->setDescription($g['description']);

            foreach($players as $p) {
                $player = new Player();
                $player->setName($p['name']);
                $player->setDevice($p['device']);
                $player->setOwner($p['owner']);
                $player->setScore($p['score']);
                $player->setGame($game);

                $manager->persist($player);
            }

            $manager->persist($game);
        }

        $manager->flush();
    }
}