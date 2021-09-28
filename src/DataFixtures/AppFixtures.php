<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cats=[];
        for($y=1; $y<=5; $y++){
            $cat= new Category;
            $cat->setName('catégorie '.$y);
            array_push($cats,$cat);
            $manager->persist($cat);
        }
        for($i=1; $i<=10; $i++){
            $post = new Post();
            $post->setTitle('Titre '.$i);
            $post->setSlug('slug'.$i);
            $post->setContent('content '.$i);
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());
            $post->setCategory($cats[array_rand($cats,1)]);
            $post->setOnline(random_int(0,1));
            $manager->persist($post);
        }

        $manager->flush();
    }
}
