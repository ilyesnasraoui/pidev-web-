<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\Offre1Type;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/offrefont")
 */
class OffrefontController extends AbstractController
{
    /**
     * @Route("/", name="offrefont_index", methods={"GET"})
     */
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offrefont/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="offrefont_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(Offre1Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('offrefont_index');
        }

        return $this->render('offrefont/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="offrefont_show", methods={"GET"})
     */
    public function show(Offre $offre): Response
    {
        return $this->render('offrefont/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    /**
     * @Route("/{idOffre}/edit", name="offrefont_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offre $offre): Response
    {
        $form = $this->createForm(Offre1Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offrefont_index');
        }

        return $this->render('offrefont/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="offrefont_delete", methods={"POST"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getIdOffre(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offrefont_index');
    }
}
