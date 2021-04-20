<?php

namespace App\Controller;

use App\Entity\Films;
use App\Form\FilmsType;
use App\Repository\FilmsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecondFilmsController extends AbstractController
{
    /**
     * @Route("/second/films", name="second_films")
     */
    public function index(): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/moviegrid.html.twig', [
            'films' => $films,
        ]);
    }
}
