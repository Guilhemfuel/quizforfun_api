<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Place;

class LoadPlacesData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setName('Tour Eiffel');
        $place->setAddress('5 Avenue Anatole France, 75007 Paris');

        $place2 = new Place();
        $place2->setName('Mont-Saint-Michel');
        $place2->setAddress('50170 Le Mont-Saint-Michel');

        $manager->persist($place);
        $manager->persist($place2);
        $manager->flush();
    }
}