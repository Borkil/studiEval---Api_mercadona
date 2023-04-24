<?php

namespace App\Tests;

use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class DealControllerTest extends WebTestCase
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
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
  }

  public function assertHasErrors(string $method, string $path, int $responseCode, string $content = null, string $responseFormat = 'json')
  {
    $crawler = $this->client->request($method, $path, content: $content);
    $this->assertResponseStatusCodeSame($responseCode);
    $this->assertResponseFormatSame($responseFormat);
  }

  public function loadFixtures(string $path)
  {
    $this->databaseTool->loadAliceFixture([$path],false);
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
  //SHOW DEAL ROUTE TEST
  public function testShowRoute()
  {
    $this->loadFixtures(__DIR__.'/loadFixtures.yaml');
    $this->assertHasErrors('GET', '/api/deal', Response::HTTP_OK);
  }

}