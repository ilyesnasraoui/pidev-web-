<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\Offre2Type;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/listeoffer")
 */
class ListeofferController extends AbstractController
{
    /**
     * @Route("/", name="listeoffer_index", methods={"GET"})
     */
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('listeoffer/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="listeoffer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(Offre2Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('listeoffer_index');
        }

        return $this->render('listeoffer/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="listeoffer_show", methods={"GET"})
     */
    public function show(Offre $offre): Response
    {
        return $this->render('listeoffer/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    /**
     * @Route("/{idOffre}/edit", name="listeoffer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offre $offre): Response
    {
        $form = $this->createForm(Offre2Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('listeoffer_index');
        }

        return $this->render('listeoffer/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="listeoffer_delete", methods={"POST"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getIdOffre(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('listeoffer_index');
    }
}
