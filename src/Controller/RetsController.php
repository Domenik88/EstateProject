<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RetsController extends AbstractController
{
    /**
     * @var string
     * rets login url
     */
    private $retsUri = 'http://data.crea.ca/Login.svc/Login';

    /**
     * @var string
     * rets username
     */
    private $retsUser = 'qeoMsug6JDuY5VrxNT3CZJGq';

    /**
     * @var string
     * rets password
     */
    private $retsPassword = 'S0uuYshvCPegrzypREFO4gdN';

    /**
     * @var string
     * rets connector version
     */
    private $retsVersion = 'RETS/1.7';

    private $rets;

    public function connect()
    {
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl($this->retsUri)
            ->setUsername($this->retsUser)
            ->setPassword($this->retsPassword)
            ->setRetsVersion($this->retsVersion);

        $this->rets = new \PHRETS\Session($config);

        return $this->rets->Login();
    }
    /**
     * @Route("/rets", name="rets")
     */
    public function index()
    {
        $connect = $this->connect();
        dump($connect);
        die;
        return $this->render('rets/index.html.twig', [
            'controller_name' => 'RetsController',
            'connector' => $connect
        ]);
    }
}
