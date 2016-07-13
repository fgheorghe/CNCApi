<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testEhlo()
    {
        $client = static::createClient();
        $client->request('GET', '/ehlo');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("application/json", $client->getResponse()->headers->get("Content-Type"));
        $this->assertEquals('{"version":{"major":0,"minor":1,"revision":0}}', $client->getResponse()->getContent());
    }
}
