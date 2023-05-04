<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $fakeCategorie = ['bricolage', 'alimentation', 'sport', 'jardin', 'meuble', 'decoration'];
        $categories = [];

        foreach ($fakeCategorie as $categorie) {
            $categories[] = (new Category())->setLabel($categorie);
        }

        for ($i=0; $i < 20; $i++) { 
            $product = (new Product())
                ->setLabel($faker->text(30))
                ->setDescription($faker->realTextBetween(10, 50))
                ->setPrice($faker->randomFloat(2,0,500))
                ->setImage($faker->image())
                ->setCategory($categories[rand(0,5)])
                ->setIsDeal(false)
                ->setIsArchive(false);

            if(rand(0, 100) % 3 === 0)
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
