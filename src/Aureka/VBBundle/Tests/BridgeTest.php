<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\Bridge;

class BridgeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itIsInstantiable()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $bridge = new Bridge($request);
    }

}