<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PostFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * @var User[]
     */
    private $users;


    /**
     * @var Category[]
     */
    private $categories;



    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();


        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

        // random user
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->username;

            $user = new User();
            $user
                ->setUsername($username)
                ->setRoles($faker->randomElements($roles, 1))
                ->setPassword($this->encoder->encodePassword($user, $username))
                ->setSlug();
            $manager->persist($user);

            $this->users[] = $user;
        }


        // add the categories
        for ($j = 0; $j < 5; $j++) { 
            $category = new Category();
            $category
                ->setTitle($faker->words(3, true))
                ->setSlug();

            $manager->persist($category);

            $this->categories[] = $category;
        }


        // add the posts
        for ($k = 1; $k <= 20; $k++) {
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

            foreach ($this->categories as $key => $value) {
                if (is_array($indexesPostCategories)) {
                    if (in_array($key, $indexesPostCategories)) {
                        $post->setCategories($value); 
                    }
                } else {
                    if ($key === $indexesPostCategories) {
                        $post->setCategories($value); 
                    }
                }
            }

            $postUser = array_rand($this->users);
            foreach ($this->users as $key => $value) {
                if ($key === $postUser) {
                    $post->setUser($value); 
                }
            }

            $manager->persist($post);


            // add comments for each posts
            for ($l = 1; $l <= mt_rand(1, 10); $l++) {
                $comment = new Comment();
                
                // interval in days between the date of creation of the post and the current date
                $now = new DateTime();
                $interval = $now->diff($post->getCreatedAt());
                $days = $interval->days;
                $minimum = '-' . $days . 'days';

                $comment->setContent($faker->sentence(15, true))
                        ->setCreatedAt($faker->dateTimeBetween($minimum))
                        ->setPost($post);

                $commentUser = array_rand($this->users);

                foreach ($this->users as $key => $value) {
                    if ($key === $commentUser) {
                        $comment->setUser($value); 
                    }
                }

                $manager->persist($comment);
            }
        }


        $manager->flush();
    }
}
