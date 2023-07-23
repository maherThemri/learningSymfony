<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $hobbies = [
            "Sports",
            "Musique",
            "Voyages",
            "Lecture",
            "Cuisine",
        ];
        for ($i = 0; $i < count($hobbies); $i++) {
            $hobby = new Hobby();
            $hobby->setDesignation($hobbies[$i]);
            $manager->persist($hobby);
        }

        $manager->flush();
    }
}
