<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Deal;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = [];

        for ($i=0; $i < 3; $i++) { 
            $categories[] = (new Category())
                ->setLabel($faker->text(15));
        }

        for ($i=0; $i < 10; $i++) { 
            $product = (new Product())
                ->setLabel($faker->text(150))
                ->setDescription($faker->realTextBetween(50, 255))
                ->setPrice($faker->randomFloat(2,0,1500))
                ->setImage($faker->image())
                ->setCategory($categories[rand(0,2)]);

            $testNumber = rand(0, 100);
            if($testNumber % 2 === 0)
            {                
                $deal = (new Deal())
                    ->setStartedAt($faker->dateTimeBetween('now', '+5 days'))
                    ->setPercentage(10);
                $deal->setFinishedAt($faker->dateTimeBetween($deal->getStartedAt(), '+10 days')); 

                $newPrice = round($product->getPrice() * (1-($deal->getPercentage()/100)), 2);

                $deal->setDealPrice($newPrice);

                $product->addDeal($deal);

            }

                $manager->persist($product);
            }
            $manager->flush();
        

    }
}
