<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 100; $i++) { 
            $article = new Article;

            $article
                ->setTitle($faker->words(6, true))
                ->setContent($faker->paragraph(4, true));

            // On stock l'image dans la base de donnée
            // $img = new Image;
            // $img->setName('cac47e108d61aacf7505b2e5e904a91e.jpg');
            // $article->addImage($img);

            $manager->persist($article);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
