<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request  $request ,AuthenticationUtils $utils): \Symfony\Component\HttpFoundation\Response
    {

        $error =$utils->getLastAuthenticationError();
        $lastUsername=$utils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'error'       => $error,
            'last_username'=>$lastUsername
        ]);

        // $this->redirect('');
    }

    /**
     * @Route("/home", name="home")
     */
    public function home(Request  $request ,AuthenticationUtils $utils): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('index.html.twig',[

            $this->getUser()->getUsername()]);

    }



    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }




}
