<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\Candidature2Type;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/depcandidat")
 */
class DepcandidatController extends AbstractController
{
    /**
     * @Route("/", name="depcandidat_index", methods={"GET"})
     */
    public function index(CandidatureRepository $candidatureRepository): Response
    {
        return $this->render('depcandidat/index.html.twig', [
            'candidatures' => $candidatureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="depcandidat_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(Candidature2Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('depcandidat_index');
        }

        return $this->render('depcandidat/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="depcandidat_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('depcandidat/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * @Route("/{idCandidature}/edit", name="depcandidat_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Candidature $candidature): Response
    {
        $form = $this->createForm(Candidature2Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('depcandidat_index');
        }

        return $this->render('depcandidat/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="depcandidat_delete", methods={"POST"})
     */
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getIdCandidature(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('depcandidat_index');
    }
}
