<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Entity\CategorieEvent;
use App\Repository\CategorieEventRepository;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;


class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement_index", methods={"GET"})
     */
    public function index(): Response
    {
        $evenements = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("evenement/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request ,CategorieEventRepository  $categorieEventRepository,UsersRepository $usersRepository): Response
    {
        $evenement = new Evenement();
        $CategorieEvents = new CategorieEvent();
        $Useress = new Users();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'CategorieEvents' => $categorieEventRepository->findAll(),
            'Userss' => $usersRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/evenement/{idEvenement}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/evenement/{idEvenement}/edit", name="evenement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evenement $evenement,CategorieEventRepository  $categorieEventRepository,UsersRepository $usersRepository): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'CategorieEvents' => $categorieEventRepository->findAll(),
            'Userss' => $usersRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/evenement/{idEvenement}", name="evenement_delete", methods={"POST"})
     */
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdEvenement(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_index');
    }

    /**
     * @Route("/showeventtt", name="evenement_indexx", methods={"GET"})
     */
    public function indexx(): Response
    {
        $evenements = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('evenement/eveenement.html.twig', [
            'evenements' => $evenements,
        ]);
    }



}
