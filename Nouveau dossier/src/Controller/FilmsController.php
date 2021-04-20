<?php

namespace App\Controller;

use App\Entity\Films;
use App\Form\FilmsType;
use App\Repository\FilmsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FilmsController extends AbstractController
{
    /**
     * @Route("/", name="films_index", methods={"GET"})
     */
    public function index(): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/index.html.twig', [
            'films' => $films,
        ]);
    }

    /**
     * @Route("/new", name="films_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $film = new Films();
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('films_index');
        }

        return $this->render('films/new.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idFilm}", name="films_show", methods={"GET"})
     */
    public function show(Films $film): Response
    {
        return $this->render('films/show.html.twig', [
            'film' => $film,
        ]);
    }

    /**
     * @Route("/{idFilm}/edit", name="films_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Films $film): Response
    {
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('films_index');
        }

        return $this->render('films/edit.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idFilm}", name="films_delete", methods={"POST"})
     */
    public function delete(Request $request, Films $film): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getIdFilm(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('films_index');
    }


    /**
     * @Route("/films/showmov", name="films_ind", methods={"GET"})
     */
    public function showmovies(): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/moviegrid.html.twig', [
            'films' => $films,
        ]);
    }




    /**
     * Search action.
     * @Route("/search/{search}", name="search")
     * @param  Request               $request Request instance
     * @param  string                $search  Search term
     * @return Response|JsonResponse          Response instance
     */
    public function searchAction(Request $request, string $search)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render("search.html.twig");
        }

        if (!$searchTerm = trim($request->query->get("search", $search))) {
            return new JsonResponse(["error" => "Search term not specified."], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        if (!($results = $em->getRepository(User::class)->findOneByEmail($searchTerm))) {
            return new JsonResponse(["error" => "No results found."], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            "html" => $this->renderView("search.ajax.twig", ["results" => $results]),
        ]);
    }
}
