<?php

namespace App\DataFixtures;

use App\Entity\Deal;
use App\Entity\Product;
use DateInterval;
use DateTimeImmutable;
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

            $testNumber = rand(0, 100);
            if($testNumber % 2 === 0)
            {

                $interval =new DateInterval('P' . rand(5,15). 'D');
                $finishDate = new DateTimeImmutable();
                


                $deal = (new Deal())
                    ->setStartedAt(new DateTimeImmutable())
                    ->setFinishedAt($finishDate->add($interval)) 
                    ->setPercentage(10);

                $newPrice = round($product->getPrice() * (1-($deal->getPercentage()/100)), 2);

                $deal->setDealPrice($newPrice);

                $product->addDeal($deal);

            }

                $manager->persist($product);
            }
            $manager->flush();

    }
}
