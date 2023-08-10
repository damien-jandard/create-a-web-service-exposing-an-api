<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user1 */
        $user1 = $this->getReference(UserFixtures::USER1_REFERENCE);
        /** @var User $user2 */
        $user2 = $this->getReference(UserFixtures::USER2_REFERENCE);

        $customers = [
            [
                'email' => 'john.doe@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe'
            ],
            [
                'email' => 'jane.smith@example.com',
                'firstName' => 'Jane',
                'lastName' => 'Smith'
            ],
            [
                'email' => 'bob.johnson@example.com',
                'firstName' => 'Bob',
                'lastName' => 'Johnson'
            ],
            [
                'email' => 'alice.williams@example.com',
                'firstName' => 'Alice',
                'lastName' => 'Williams'
            ],
            [
                'email' => 'peter.james@example.com',
                'firstName' => 'Peter',
                'lastName' => 'James'
            ],
            [
                'email' => 'susan.miller@example.com',
                'firstName' => 'Susan',
                'lastName' => 'Miller'
            ],
            [
                'email' => 'ari.lewis@example.com',
                'firstName' => 'Ari',
                'lastName' => 'Lewis'
            ],
            [
                'email' => 'louis.richardson@example.com',
                'firstName' => 'Louis',
                'lastName' => 'Richardson'
            ],
            [
                'email' => 'ruby.scott@example.com',
                'firstName' => 'Ruby',
                'lastName' => 'Scott'
            ],
            [
                'email' => 'david.horton@example.com',
                'firstName' => 'David',
                'lastName' => 'Horton'
            ],
            [
                'email' => 'julius.kelly@example.com',
                'firstName' => 'Julius',
                'lastName' => 'Kelly'
            ],
            [
                'email' => 'brianna.mills@example.com',
                'firstName' => 'Brianna',
                'lastName' => 'Mills'
            ],
            [
                'email' => 'evangeline.gilbert@example.com',
                'firstName' => 'Evangeline',
                'lastName' => 'Gilbert'
            ],
            [
                'email' => 'leonardo.perkins@example.com',
                'firstName' => 'Leonardo',
                'lastName' => 'Perkins'
            ],
            [
                'email' => 'rosalie.hill@example.com',
                'firstName' => 'Rosalie',
                'lastName' => 'Hill'
            ]
        ];

        foreach ($customers as $c) {
            $customer = new Customer();
            rand(0, 1) === 0 ? $customer->setOwner($user1) : $customer->setOwner($user2);
            $customer->setEmail($c['email'])
                ->setFirstName($c['firstName'])
                ->setLastName($c['lastName']);
            $manager->persist($customer);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
