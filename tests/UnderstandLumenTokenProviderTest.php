<?php

use Understand\UnderstandLumen\TokenProvider;

class UnderstandTokenProviderTest extends PHPUnit_Framework_TestCase
{
    public function testRandomToken()
    {
        $tokenProvider = new TokenProvider();
        $initialToken = $tokenProvider->getToken();
        $this->assertNotEmpty($initialToken);
        $this->assertSame($initialToken, $tokenProvider->getToken());
        $tokenProvider->generate();
        $this->assertNotSame($initialToken, $tokenProvider->getToken());
    }
}