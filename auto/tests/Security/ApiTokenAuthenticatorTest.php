<?php

namespace App\Tests\Security;

use App\Security\ApiTokenAuthenticator;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiTokenAuthenticatorTest extends TestCase
{
    public function testSupportsAndAuthenticate()
    {
        $user = new \App\Entity\User();
        $user->setEmail('test@example');
        $user->setPassword('x');
        $user->setApiToken('secret');

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneBy')->with(['apiToken' => 'secret'])->willReturn($user);

        $auth = new ApiTokenAuthenticator($repo);

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/trips');
        $request->headers->set('Authorization', 'Bearer secret');

        $this->assertTrue($auth->supports($request));
        $passport = $auth->authenticate($request);
        $this->assertNotNull($passport);
    }
}
