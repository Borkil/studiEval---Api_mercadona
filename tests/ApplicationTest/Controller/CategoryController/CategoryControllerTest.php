<?php

namespace App\Tests;

use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class CategoryControllerTest extends WebTestCase
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

  public function categoryJson()
  {
    $faker = Factory::create('fr_FR');
    return json_encode([
      "label" => $faker->name()
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
  //SHOW CATEGORY ROUTE TEST
  public function testShowCategoryRoute()
  {
    $this->assertHasErrors('GET', '/api/category', Response::HTTP_OK);
  }
  
  //CREATE CATEGORY ROUTE TEST
  public function testCreateCategoryRoute()
  {
    $this->assertHasErrors('POST', '/api/category', Response::HTTP_CREATED, $this->categoryJson());
  }

  //UPDATE ROUTE TEST
  public function testUpdateCategoryRoute()
  {
    $this->loadFixtures(__DIR__.'/UniqueFixturesCategory.yaml');
    $this->assertHasErrors('PUT', '/api/category/1', Response::HTTP_ACCEPTED, $this->categoryJson());
  }

  public function testShould_BadRequest_WhenPostInvalidFormatJson()
  {
    //Create route test
    $this->assertHasErrors('POST', '/api/category', Response::HTTP_BAD_REQUEST, $this->getInvalidJsonFormat());

    //Update route test
    $this->assertHasErrors('PUT', '/api/category/1', Response::HTTP_BAD_REQUEST, $this->getInvalidJsonFormat());
  }

  public function testShould_BadRequest_WhenPostInvalidCategory()
  {
    //Create route test
    $this->assertHasErrors('POST', '/api/category', Response::HTTP_BAD_REQUEST, $this->getInvalidData());

    //Update route test
    $this->assertHasErrors('PUT', '/api/category/1', Response::HTTP_BAD_REQUEST, $this->getInvalidData());
  }

  public function testShould_BadRequest_WhenPostNotUniqueCategory()
  {
    $this->loadFixtures(__DIR__.'/UniqueFixturesCategory.yaml');
    $content = '{
      "label" = "test"
    }';
    //Create route test
    $this->assertHasErrors('POST', '/api/category', Response::HTTP_BAD_REQUEST, $content);

    //Update route test
    $this->assertHasErrors('PUT', '/api/category/1', Response::HTTP_BAD_REQUEST, $content);
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