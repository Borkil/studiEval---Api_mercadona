<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $fakeCategorie = ['bricolage', 'alimentation', 'sport', 'jardin', 'meuble', 'decoration'];
        $categories = [];
        $user = (new User())
            ->setEmail('admin@mail.fr')
            ->setPassword('$2y$13$GhoXHGjGzg5DvulYWQprvOdXlgrXntKoultYX39rnrFAq4DV7zDDS');
        $manager->persist($user);

        foreach ($fakeCategorie as $categorie) {
            $categories[] = (new Category())->setLabel($categorie);
        }

        for ($i=1; $i < 11; $i++) { 
            $product = (new Product())
                ->setLabel($faker->text(30))
                ->setDescription($faker->realTextBetween(10, 50))
                ->setPrice($faker->randomFloat(2,0,500))
                ->setImage('image0'. $i . '.jpg')
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
