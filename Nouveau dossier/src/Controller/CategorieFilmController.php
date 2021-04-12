<?php

namespace App\Controller;

use App\Entity\CategorieFilm;
use App\Form\CategorieFilmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie/film")
 */
class CategorieFilmController extends AbstractController
{
    /**
     * @Route("/", name="categorie_film_index", methods={"GET"})
     */
    public function index(): Response
    {
        $categorieFilms = $this->getDoctrine()
            ->getRepository(CategorieFilm::class)
            ->findAll();

        return $this->render('categorie_film/index.html.twig', [
            'categorie_films' => $categorieFilms,
        ]);
    }

    /**
     * @Route("/new", name="categorie_film_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorieFilm = new CategorieFilm();
        $form = $this->createForm(CategorieFilmType::class, $categorieFilm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieFilm);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_film_index');
        }

        return $this->render('categorie_film/new.html.twig', [
            'categorie_film' => $categorieFilm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCategorie}", name="categorie_film_show", methods={"GET"})
     */
    public function show(CategorieFilm $categorieFilm): Response
    {
        return $this->render('categorie_film/show.html.twig', [
            'categorie_film' => $categorieFilm,
        ]);
    }

    /**
     * @Route("/{idCategorie}/edit", name="categorie_film_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieFilm $categorieFilm): Response
    {
        $form = $this->createForm(CategorieFilmType::class, $categorieFilm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_film_index');
        }

        return $this->render('categorie_film/edit.html.twig', [
            'categorie_film' => $categorieFilm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCategorie}", name="categorie_film_delete", methods={"POST"})
     */
    public function delete(Request $request, CategorieFilm $categorieFilm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieFilm->getIdCategorie(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieFilm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_film_index');
    }
}
