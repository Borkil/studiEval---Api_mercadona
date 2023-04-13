<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/product');

        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseFormatSame('json');
    }
}
