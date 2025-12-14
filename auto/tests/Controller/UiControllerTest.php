<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UiControllerTest extends WebTestCase
{
    public function testVehicleFormAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/vehicles/new');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Create Vehicle', $client->getResponse()->getContent());
    }

    public function testTripFormAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/trips/new');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Create Trip', $client->getResponse()->getContent());
    }
}
