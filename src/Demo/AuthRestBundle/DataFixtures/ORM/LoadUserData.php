<?php

namespace Demo\AuthRestBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Demo\AuthRestBundle\Entity\User;

class LoadUserData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setNom('nom1');
        $user1->setPrenom('prenom1');
        $user1->setEmail('user1@test.org');
        $user1->setPassword('pass1');

        $user2 = new User();
        $user2->setNom('nom2');
        $user2->setPrenom('prenom2');
        $user2->setEmail('user2@test.org');
        $user2->setPassword('pass2');

        $manager->persist($user1);
        $manager->persist($user2);

        $manager->flush();
    }
}