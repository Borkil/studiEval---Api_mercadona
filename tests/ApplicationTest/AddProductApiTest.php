<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddProductApiTest extends WebTestCase
{
    // quand un produit est bien creer on retourne une reponse au statut 201
    // le content de la reponse est un json avec les informations suivante 
    // retourne le produit au format json avec le nouvelle {id}

    // quand le format des données n'est pas ok alors on retourne une erreur 400

    public function testShould_ResponseValid_When_ProductIsAddInDatabase(): void
    {
        $data = json_encode([
            'label' => 'mon label',
            'description' => 'une description d\'article valide',
            'price' => '20,15',
            'image' => 'uneimage.jpeg'
        ]);



        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', content: $data);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }
}
