<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Brdeju\Bundle\CurrenciesChartsBundle\Controller\SecurityController;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        
        $securityCtrl = new SecurityController();
        $result = $securityCtrl->loginAction();
        
        var_dump( $result );
        die();
    }

    public function testLogincheck()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login_check');
    }

}
