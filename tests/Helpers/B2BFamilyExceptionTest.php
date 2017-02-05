<?php

class B2BFamilyExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AmoCRM\Helpers\B2BFamilyException
     * @expectedExceptionCode 100
     * @expectedExceptionMessage message
     */
    public function testThrowException()
    {
        throw new \AmoCRM\Helpers\B2BFamilyException('message', 100);
    }
}