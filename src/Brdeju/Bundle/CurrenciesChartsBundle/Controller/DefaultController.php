<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello", name="hello_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
