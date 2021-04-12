<?php

namespace App\Controller;

use App\Entity\CategorieEvent;
use App\Form\CategorieEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie/event")
 */
class CategorieEventController extends AbstractController
{
    /**
     * @Route("/", name="categorie_event_index", methods={"GET"})
     */
    public function index(): Response
    {
        $categorieEvents = $this->getDoctrine()
            ->getRepository(CategorieEvent::class)
            ->findAll();

        return $this->render('categorie_event/index.html.twig', [
            'categorie_events' => $categorieEvents,
        ]);
    }

    /**
     * @Route("/new", name="categorie_event_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorieEvent = new CategorieEvent();
        $form = $this->createForm(CategorieEventType::class, $categorieEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieEvent);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_event_index');
        }

        return $this->render('categorie_event/new.html.twig', [
            'categorie_event' => $categorieEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCatEvent}", name="categorie_event_show", methods={"GET"})
     */
    public function show(CategorieEvent $categorieEvent): Response
    {
        return $this->render('categorie_event/show.html.twig', [
            'categorie_event' => $categorieEvent,
        ]);
    }

    /**
     * @Route("/{idCatEvent}/edit", name="categorie_event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieEvent $categorieEvent): Response
    {
        $form = $this->createForm(CategorieEventType::class, $categorieEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_event_index');
        }

        return $this->render('categorie_event/edit.html.twig', [
            'categorie_event' => $categorieEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCatEvent}", name="categorie_event_delete", methods={"POST"})
     */
    public function delete(Request $request, CategorieEvent $categorieEvent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieEvent->getIdCatEvent(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_event_index');
    }
}
