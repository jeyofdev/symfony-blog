<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // admin user
        $user = new User();
        $user
            ->setUsername('admin')
            ->setPassword($this->encoder->encodePassword($user, 'admin'));

        // random user
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->username;

            $user = new User();
            $user
                ->setUsername($username)
                ->setPassword($this->encoder->encodePassword($user, $username));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
