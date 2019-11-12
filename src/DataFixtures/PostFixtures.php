<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // add 20 posts
        for ($i = 1; $i <= 20; $i++) {
            $post = new Post();
            $post->setTitle($faker->words(3, true))
                ->setContent($faker->paragraphs(rand(5, 20), true))
                ->setCreatedAt($faker->dateTimeBetween("-3 years"));

            $manager->persist($post);
        }

        $manager->flush();
    }
}
