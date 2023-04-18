<?php

namespace App\Tests;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{

    public function getEntity ()
    {
        return (new Category())
            ->setLabel('Bicolage');
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
    public function assertHasErrors(Category $entity, int $errorNumber): void
    {
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($entity);
        $this->assertCount($errorNumber, $error);
    }

    public function testEntityIsValid()
    {
        return $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testShould_invalid_When_labelIsBlank()
    {
        return $this->assertHasErrors($this->getEntity()->setLabel(''), 1);
    }


    /**
     * Test si un label est unique en bdd
     * doit retourner une erreurs si le produit existe deja en bdd
     */
    public function testShould_invalid_When_labelAlreadyExists()
    {
        $this->assertHasErrors($this->getEntity()->setLabel('test'),1);
    }

    
}
