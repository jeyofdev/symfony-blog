<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture
{
    /**
     * @var Category[]
     */
    private $categories;



    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();


        // add the categories
        for ($i = 0; $i < 5; $i++) { 
            $category = new Category();
            $category
                ->setTitle($faker->words(3, true))
                ->setSlug();

            $manager->persist($category);

            $this->categories[] = $category;
        }


        // add the posts
        for ($j = 1; $j <= 20; $j++) {
            $post = new Post();
            $post
                ->setTitle($faker->words(3, true))
                ->setContent($faker->paragraphs(rand(5, 20), true))
                ->setCreatedAt($faker->dateTimeBetween("-3 years"))
                ->setUpdatedAt($post->getCreatedAt())
                ->setSlug()
                ->setPublished(rand(0, 1));

            $countCategories = count($this->categories);
            $indexesPostCategories = array_rand($this->categories, mt_rand(1, $countCategories));

            foreach ($this->categories as $k => $v) {
                if (is_array($indexesPostCategories)) {
                    if (in_array($k, $indexesPostCategories)) {
                        $post->setCategories($v); 
                    }
                } else {
                    if ($k === $indexesPostCategories) {
                        $post->setCategories($v); 
                    }
                }
            }

            $manager->persist($post);


            // add comments for each posts
            for ($k = 1; $k <= mt_rand(0, 10); $k++) {
                $comment = new Comment();
                
                // interval in days between the date of creation of the post and the current date
                $now = new DateTime();
                $interval = $now->diff($post->getCreatedAt());
                $days = $interval->days;
                $minimum = '-' . $days . 'days';

                $comment->setUser($faker->username)
                        ->setContent($faker->sentence(15, true))
                        ->setCreatedAt($faker->dateTimeBetween($minimum))
                        ->setPost($post);

                $manager->persist($comment);
            }
        }


        $manager->flush();
    }
}
