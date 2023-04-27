<?php

namespace App\Tests;

use App\Entity\Product;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{

    public function getEntity ()
    {
        return (new Product())
            ->setLabel('Mon produit')
            ->setDescription('Mon produit a un description')
            ->setPrice(20.02)
            ->setImage('nomdelimage.jpeg')
            ->setIsDeal(false)
            ->setIsArchive(false);
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
    public function assertHasErrors(Product $entity, int $errorNumber): void
    {
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($entity);
        $this->assertCount($errorNumber, $error);
    }

    public function testEntityIsValid(): void
    {
        $this->assertHasErrors($this->getEntity(),0);
    }

    public function testShould_invalid_When_labelIsBlank (): void
    {
        $this->assertHasErrors($this->getEntity()->setLabel(''), 1);
    }

    public function testShould_invalid_When_descriptionIsBlank (): void
    {
        $this->assertHasErrors($this->getEntity()->setDescription(''), 1);
    }

    /**
     * lorsque le champs isDeal est = true alors les champs d'imformation de deal != de null
     * finishDealAt
     * percentage
     * priceDeal
     *
     * @return void
     */
    public function testShould_DealInformationIsNotNull_When_IsDealEqualTrue(): void
    {
        $this->assertHasErrors($this->getEntity()->setIsDeal(true), 3);
    }
    
    /**
     * lorsque le champs isDeal est = true alors le priceDeal et le percentage doivent etre positif
     * percentage
     * priceDeal
     *
     * @return void
     */
    public function testShould_DealInformationArePositive_When_IsDealEqualTrue(): void
    {
        $entity = $this->getEntity()
            ->setIsDeal(true)
            ->setFinishDealAt(new DateTimeImmutable())
            ->setPercentage(-10)
            ->setPriceDeal(-10.00);

        $this->assertHasErrors($entity, 2);
    }

    /**
     * lorsque le champs isDeal est = true alors le priceDeal et le percentage doivent etre positif
     * percentage
     * priceDeal
     *
     * @return void
     */
    public function testShould_DealInformationIsNotEqualToZero_When_IsDealEqualTrue(): void
    {
        $entity = $this->getEntity()
            ->setIsDeal(true)
            ->setFinishDealAt(new DateTimeImmutable())
            ->setPercentage(0)
            ->setPriceDeal(0.00);

        $this->assertHasErrors($entity, 2);
    }

    /**
     * lorsque le champs isDeal est = false alors les champs d'imformation de deal === null
     * finishDealAt
     * percentage
     * priceDeal
     *
     * @return void
     */
    public function testShould_DealInformationIsNull_When_IsDealEqualFalse(): void
    {
        $entity = $this->getEntity()
            ->setFinishDealAt(new DateTimeImmutable())
            ->setPercentage(10)
            ->setPriceDeal(null);

        $this->assertHasErrors($entity, 2);
    }

    /**
     * lorsque le champs isArchive === true alors le champs isDeal === false
     *
     * @return void
     */
    public function testShould_isDealEqualFalse_When_IsArchiveEqualTrue(): void
    {
        $entity = $this->getEntity()
            ->setIsDeal(true)
            ->setIsArchive(true)
            ->setFinishDealAt(new DateTimeImmutable())
            ->setPercentage(10)
            ->setPriceDeal(10.01);

        $this->assertHasErrors($entity, 1);
    }

}
