<?php

namespace Demo\AuthRestBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testNotAuthenticated()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("LOGIN")')->count() > 0);
    }

    public function testBadAuthentication()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('submit_login')->form();

        $form['_username'] = 'bad';
        $form['_password'] = 'bad';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("LOGIN")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Bad credentials")')->count() > 0);
    }

    public function testGoodAuthentication()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('submit_login')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'adminpass';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("LIST")')->count() > 0);
    }

    public function testLogout()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('submit_login')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'adminpass';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Logout')->link();

        $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("LOGIN")')->count() > 0);
    }
}
