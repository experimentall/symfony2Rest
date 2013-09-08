<?php

namespace Demo\AuthRestBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Demo\AuthRestBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    public function __construct() {
 
        $kernelNameClass = $this->getKernelClass();
        $kernel = new $kernelNameClass('test', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }


    private function cleanData()
    {
        $entity = $this->em->getRepository('DemoAuthRestBundle:User')
            ->findOneByEmail('userTest@test.org');

        if ($entity)
        {
            $this->em->remove($entity);
            $this->em->flush();
        }
    }


    protected function tearDown()
    {
        $this->cleanData();
    }


    protected function setUp()
    {
        $this->cleanData();
    }


    public function testAddUserRequiredParameters()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            ''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());// 400 Bad Request
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(
            '{"form":{"children":{"nom":{"errors":["This value should not be blank."]},"prenom":{"errors":["This value should not be blank."]},"email":{"errors":["This value should not be blank."]},"password":{"errors":["This value should not be blank."]}}}}',
            $client->getResponse()->getContent()
        );

    }


    public function testAddUserEmailValidator()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest", "password": "passTest"}'
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(
            '{"form":{"children":{"nom":[],"prenom":[],"email":{"errors":["This value is not a valid email address."]},"password":[]}}}',
            $client->getResponse()->getContent()
        );

    }


    public function testAddUserSuccess()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp(
            '/{"nom":"nomTest","prenom":"prenomTest","email":"userTest@test.org"}/',
            $client->getResponse()->getContent()
        );
    }


    public function testAddUniqueEmail()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );
        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }


    public function testUpdateUserNotExist()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTestNonExist@test.org", "password": "passTest"}'
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }


    public function testUpdateUserSuccess()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );
        $client->request(
            'PUT',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTestUpdate", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp(
            '/{"nom":"nomTestUpdate","prenom":"prenomTest","email":"userTest@test.org"}/',
            $client->getResponse()->getContent()
        );
    }


    public function testGetUserNotExist()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/get/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"email": "userTestNonExist@test.org"}'
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }


    public function testGetUser()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );

        $client->request(
            'POST',
            '/api/get/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"email": "userTest@test.org"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp(
            '/{"nom":"nomTest","prenom":"prenomTest","email":"userTest@test.org"}/',
            $client->getResponse()->getContent()
        );
    }


    public function testDeleteUser()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"nom": "nomTest", "prenom": "prenomTest", "email": "userTest@test.org", "password": "passTest"}'
        );

        $client->request(
            'DELETE',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"email": "userTest@test.org"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp(
            '/{"delete":{"email":"userTest@test.org","msg":"delete success"}}/',
            $client->getResponse()->getContent()
        );
    }

    public function testDeleteUserNotExist()
    {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/user',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ),
            '{"email": "userTestNotExist@test.org"}'
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

}