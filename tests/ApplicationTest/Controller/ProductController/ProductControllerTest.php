<?php

namespace App\Tests;

use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class ProductControllerTest extends WebTestCase
{
  /**
  * databaseTool
  * @var AbstractDatabaseTool
  */
  protected $databaseTool;

  /**
  * client
  *
  * @var KernelBrowser
  */
  private $client;

  public function setUp(): void
  {
    parent::setUp();
    $this->client = static::createClient();
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();;
  }

  public function assertHasErrors(string $method, string $path, int $responseCode, string $content = null, string $responseFormat = 'json')
  {
    $crawler = $this->client->request($method, $path, content: $content);
    $this->assertResponseStatusCodeSame($responseCode);
    $this->assertResponseFormatSame($responseFormat);
  }

  public function loadFixtures(string $path)
  {
    $this->databaseTool->loadAliceFixture([$path],false, null, 'doctrine', ORMPurger::PURGE_MODE_TRUNCATE );
  }

  public function productJson()
  {
    $faker = Factory::create('fr_FR');
    return json_encode([
      "label" => $faker->text(30),
      "description" => $faker->realTextBetween(50, 250),
      "price" => $faker->randomFloat(2, 10, 2000),
      "image" => $faker->image()
    ]);
  }

  public function getInvalidData()
  {
    return '{
      "label" : ""
    }';
  }

  public function getInvalidJsonFormat()
  {
    return '{
      "label" : ""
    ';
  }

  //TEST
  //SHOW product ROUTE TEST
  public function testShowProductRoute()
  {
    $this->assertHasErrors('GET', '/api/product', Response::HTTP_OK);
  }
  
  //CREATE product ROUTE TEST
  public function testCreateProductRoute()
  {
    $this->assertHasErrors('POST', '/api/product', Response::HTTP_CREATED, $this->productJson());
  }

//   //UPDATE ROUTE TEST
//   public function testUpdateProductRoute()
//   {
//     $this->loadFixtures(__DIR__.'/UniqueFixturesProduct.yaml');
//     $this->assertHasErrors('POST', '/api/product/1', Response::HTTP_ACCEPTED, $this->productJson());
//   }

//   public function testShould_BadRequest_WhenPostInvalidFormatJson()
//   {
//     //Create route test
//     $this->assertHasErrors('POST', '/api/product', Response::HTTP_BAD_REQUEST, $this->getInvalidJsonFormat());

//     //Update route test
//     $this->assertHasErrors('POST', '/api/product/1', Response::HTTP_BAD_REQUEST, $this->getInvalidJsonFormat());
//   }

//   public function testShould_BadRequest_WhenPostInvalidProduct()
//   {
//     //Create route test
//     $this->assertHasErrors('POST', '/api/product', Response::HTTP_BAD_REQUEST, $this->getInvalidData());

//     //Update route test
//     $this->assertHasErrors('POST', '/api/product/1', Response::HTTP_BAD_REQUEST, $this->getInvalidData());
//   }

//   public function testShould_BadRequest_WhenPostNotUniqueProduct()
//   {
//     $this->loadFixtures(__DIR__.'/UniqueFixturesProduct.yaml');
//     $content = '{
//       "label" = "test"
//     }';
//     //Create route test
//     $this->assertHasErrors('POST', '/api/product', Response::HTTP_BAD_REQUEST, $content);

//     //Update route test
//     $this->assertHasErrors('POST', '/api/product/1', Response::HTTP_BAD_REQUEST, $content);
//   }

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
