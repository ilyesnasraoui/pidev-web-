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
     * @Route("/test2", name="test")
     */
    public function indeex(): Response
    {
        return $this->render('users/signup.html.twig', [
            'controller_name' => 'TestController',
        ]);

    }
}
