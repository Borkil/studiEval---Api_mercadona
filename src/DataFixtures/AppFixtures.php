<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 10; $i++) { 
            $product = (new Product())
                ->setLabel($faker->text(150))
                ->setDescription($faker->realTextBetween(50, 255))
                ->setPrice($faker->randomFloat(2,0,1500))
                ->setImage($faker->image());
                $manager->persist($product);
            }
            $manager->flush();

    }
}
