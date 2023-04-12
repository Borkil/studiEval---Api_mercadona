<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTest extends KernelTestCase
{

    public function getEntity ()
    {
        return (new Product())
            ->setLabel('Mon produit')
            ->setDescription('Mon produit a un description')
            ->setPrice('27,21')
            ->setImage('nomdelimage.jpeg');
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

    public function testEntityIsValid()
    {
        $this->assertHasErrors($this->getEntity(),0);
    }

    public function testShould_invalid_When_labelIsBlank ()
    {
        $this->assertHasErrors($this->getEntity()->setLabel(''), 1);
    }

    public function testShould_invalid_When_descriptionIsBlank ()
    {
        $this->assertHasErrors($this->getEntity()->setDescription(''), 1);
    }
    
    public function testShould_valid_When_priceTemplateIsOK()
    {
        $this->assertHasErrors($this->getEntity()->setPrice('27,00'), 0);
        $this->assertHasErrors($this->getEntity()->setPrice('465121327,00'), 0);
        $this->assertHasErrors($this->getEntity()->setPrice('0,12'), 0);
        $this->assertHasErrors($this->getEntity()->setPrice('0,00'), 0);
    }

    public function testShould_invalid_When_priceTemplateIsNotOk()
    {
        $this->assertHasErrors($this->getEntity()->setPrice('27,123'), 1);
        $this->assertHasErrors($this->getEntity()->setPrice('a27,12'), 1);
        $this->assertHasErrors($this->getEntity()->setPrice('27,a1'), 1);
        $this->assertHasErrors($this->getEntity()->setPrice('27'), 1);
        $this->assertHasErrors($this->getEntity()->setPrice('27,'), 1);
        $this->assertHasErrors($this->getEntity()->setPrice('27,00ad'), 1);
    }

    public function testShould_invalid_When_priceIsNegative()
    {
        $this->assertHasErrors($this->getEntity()->setPrice('-10,12'),2);
    }
}
