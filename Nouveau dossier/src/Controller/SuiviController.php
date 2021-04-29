<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\Candidature3Type;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/suivi")
 */
class SuiviController extends AbstractController
{
    /**
     * @Route("/", name="suivi_index", methods={"GET"})
     */
    public function index(CandidatureRepository $candidatureRepository): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $candidatures = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->findByIdUser($user->getIdUser());

        return $this->render('suivi/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    /**
     * @Route("/new", name="suivi_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(Candidature3Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('suivi_index');
        }

        return $this->render('suivi/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="suivi_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('suivi/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * @Route("/{idCandidature}/edit", name="suivi_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Candidature $candidature): Response
    {
        $form = $this->createForm(Candidature3Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('suivi_index');
        }

        return $this->render('suivi/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="suivi_delete", methods={"POST"})
     */
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getIdCandidature(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('suivi_index');
    }
}
