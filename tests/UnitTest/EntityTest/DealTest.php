<?php

namespace App\Tests;

use App\Entity\Deal;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DealTest extends KernelTestCase
{
    public function getEntity ()
    {
        return (new Deal())
            ->setStartedAt(new DateTime('15-05-2023'))
            ->setFinishedAt(new DateTime('25-08-2023'))
            ->setPercentage(10)
            ->setDealPrice(round((100 * 0.90),2));
    }

    /**
     * assertHasErrors
     *boot le kernel et le container
    *charge le service Validator et valide une entité
    *fait un assertCount
    *
    * @param  Product $entity - une entité à tester
    * @param  int $errorNumber - nombre d'erreur attendu
    * @return void
    */
    public function assertHasErrors(Deal $entity, int $errorNumber): void
    {
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($entity);
        $this->assertCount($errorNumber, $error);
    }

    public function testEntityIsValid(): void
    {
        $this->assertHasErrors($this->getEntity(),0);
    }

    public function testShould_invalid_When_percentageIsNotInRange()
    {
        $this->assertHasErrors($this->getEntity()->setPercentage(0),1);
        $this->assertHasErrors($this->getEntity()->setPercentage(-1),1);
        $this->assertHasErrors($this->getEntity()->setPercentage(100),1);
        $this->assertHasErrors($this->getEntity()->setPercentage(101),1);
        $this->assertHasErrors($this->getEntity()->setPercentage(50),0);
    }

    public function testShould_invalid_When_priceDealLessThanOrEqualToZero()
    {
        $this->assertHasErrors($this->getEntity()->setDealPrice(0),1);
        $this->assertHasErrors($this->getEntity()->setDealPrice(-1),1);
    }
}
