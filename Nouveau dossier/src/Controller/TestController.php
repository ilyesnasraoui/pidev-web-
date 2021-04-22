<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/front", name="front")
     */
    public function indeex(): Response
    {
        return $this->render('indexfront.html.twig', [
            'controller_name' => 'TestController',
        ]);

    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'TestController',
        ]);

    }


    /**
     * @Route("/testlyes", name="testlyes")
     */
    public function testlyes(): Response
    {
        return $this->render('films/moviegrid.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
