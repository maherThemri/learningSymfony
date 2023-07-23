<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Profile;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://www.facebook.com/meher.themri/');

        $profile1 = new Profile();
        $profile1->setRs('Linkedin');
        $profile1->setUrl('https://www.linkedin.com/in/thamri-maher-1a039118a/');
        $manager->persist($profile);
        $manager->persist($profile1);
        $manager->flush();
    }
}
