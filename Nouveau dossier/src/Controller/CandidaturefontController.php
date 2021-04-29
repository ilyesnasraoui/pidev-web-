<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\Candidature1Type;
use App\Repository\CandidatureRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/candidaturefont")
 */
class CandidaturefontController extends AbstractController
{
    /**
     * @Route("/", name="candidaturefont_index", methods={"GET"})
     */
    public function index(CandidatureRepository $candidatureRepository,Request $request,PaginatorInterface  $paginator): Response
    {
        $candidature = $this->getDoctrine()->getRepository(Candidature::class);
        $candidatures = $candidature->findAll();
        // Paginate the results of the query
        $candidatures = $paginator->paginate(
        // Doctrine Query, not results
            $candidatures,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('candidaturefont/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    /**
     * @Route("/new", name="candidaturefont_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(Candidature1Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('candidaturefont_index');
        }

        return $this->render('candidaturefont/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="candidaturefont_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidaturefont/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * @Route("/{idCandidature}/edit", name="candidaturefont_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Candidature $candidature): Response
    {
        $form = $this->createForm(Candidature1Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidaturefont_index');
        }

        return $this->render('candidaturefont/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="candidaturefont_delete", methods={"POST"})
     */
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getIdCandidature(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidaturefont_index');
    }
}
