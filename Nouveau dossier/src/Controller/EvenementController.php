<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Entity\CategorieEvent;
use App\Repository\CategorieEventRepository;
use Symfony\Component\Routing\Annotation\Route;
class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement_index", methods={"GET"})
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $evenements=$paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            4
        );

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("evenement/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request,UsersRepository  $UsersRepository,CategorieEventRepository $CategorieEventRepository): Response
    {
        $evenement = new Evenement();
        $users =new Users();
        $categorieevent = new CategorieEvent();
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
            'users' => $UsersRepository->findAll(),
            'categorieevent'=> $CategorieEventRepository->findAll(),
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
    public function edit(Request $request, Evenement $evenement,UsersRepository  $UsersRepository,CategorieEventRepository $CategorieEventRepository): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        $users =new Users();
        $categorieevent = new CategorieEvent();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'users' => $UsersRepository->findAll(),
            'categorieevent'=> $CategorieEventRepository->findAll(),
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
    public function indexx(Request $request,PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $evenements=$paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            4);

        return $this->render('evenement/eveenement.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/stats", name="stats")
     */

     public function statistiques(CategorieEventRepository $CategorieEventRepository ,EvenementRepository $evenementRepository){

       $categories=$CategorieEventRepository->findAll();
       $evenements=$evenementRepository->findAll();
          $catnom=[];
          $catcount=[];
          $i=0;


         foreach($categories as $categorieEvent)
         {
             $catnom[]=$categorieEvent->getNomCategorieEv();

             $i=0;

             foreach($evenements as $evenement)
             {
                 if($evenement->getIdCatEvenement()==$catnom){
                     $i=$i+1;
                 }

             }
             $catcount[]=$i;

         }


         return $this->render('evenement/stats.html.twig',[
             "evenement" => $evenements,
             "categories" => $categories,'nom'=>json_encode($catnom),'count'=>json_encode($catcount)

         ]);
     }



}
