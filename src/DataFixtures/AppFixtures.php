<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\ProductStatus;
use DateTimeImmutable;
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
                ->setCategory($categories[rand(0,2)])
                ->setIsDeal(false)
                ->setIsArchive(false);

            if(rand(0, 100) % 2 === 0)
            {   
                $product->setFinishDealAt(new DateTimeImmutable("+10 days"));
                $product->setPercentage(10);
                $newPrice= round((1 - ($product->getPercentage() / 100)) * $product->getPrice(),2);
                $product->setPriceDeal($newPrice);
                $product->setIsDeal(true);
            }

                $manager->persist($product);
            }
            $manager->flush();
        

    }
}
