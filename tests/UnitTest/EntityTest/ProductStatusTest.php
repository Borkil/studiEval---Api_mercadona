<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\ProductStatus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class ProductStatusTest extends KernelTestCase
{
   /**
     * databaseTool
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;
    
    public function getEntity ()
    {
        return (new ProductStatus())
            ->setLabel('onSale');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    /**
     * assertHasErrors
     *boot le kernel et le container
    *charge le service Validator et valide une entité
    *fait un assertCount
    *
    * @param  ProductStatus $entity - une entité à tester
    * @param  int $errorNumber - nombre d'erreur attendu
    * @return void
    */
    public function assertHasErrors(ProductStatus $entity, int $errorNumber): void
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
        $this->databaseTool->loadAliceFixture([__DIR__.'/ProductStatusTestFixtures.yaml'], false);
        $this->assertHasErrors($this->getEntity()->setLabel('test'),1);
    }

        
    /**
     * tearDown function for LiipTestFixturesBundles
     *
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
