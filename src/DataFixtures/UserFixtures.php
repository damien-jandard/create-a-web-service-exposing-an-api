<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER1_REFERENCE = 'user1';
    public const USER2_REFERENCE = 'user2';
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('user@sprint.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user1, 'User1234*'))
            ->setCompany('Sprint');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user@verizon.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user2, 'User1234*'))
            ->setCompany('Verizon');
        $manager->persist($user2);

        $manager->flush();

        $this->addReference(self::USER1_REFERENCE, $user1);
        $this->addReference(self::USER2_REFERENCE, $user2);
    }
}
