<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\FilmsRepository;
use App\Repository\SalleRepository;
use App\Repository\UsersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/salle")
 */
class SalleController extends AbstractController
{

    /**
     * @Route("/salleplanning", name="salleplanning", methods={"GET","POST"})
     */
    public function sp(Request $request,PaginatorInterface $paginator,SalleRepository  $sr,FilmsRepository $fr)
    {
       // return new Response($request->get('idsalle'));
        $donnees = $this->getDoctrine()
            ->getRepository(Salle::class)
            ->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Planning p
            WHERE  
             (p.idSalle = :id)
             ORDER BY p.date
            '
        )
            ->setParameters(array('id' => $request->get('idsalle')));
        return $this->render('salle/showplanningsalle.html.twig', [
            'salle' =>$sr->findOneBySomeField($request->get('idsalle')) ,
            'plannings' => $query->getResult(),
            'films' => $fr->findAll(),
        ]);

    }

    /**
     * @Route("/search", name="salle_search", methods={"POST","GET"})
     */
    public function searchAction(Request $request,SalleRepository  $sr, UsersRepository $ur)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $name=$request->get("roomname");
        $gov=$request->get("gov");


       if($gov =="") {
           $query = $entityManager->createQuery(
               'SELECT p
            FROM App\Entity\Salle p
            WHERE  
             (p.name LIKE :name)
            '
           )
               ->setParameters(array('name' => "%" . $name . "%"));
       }
       else
       {
           $query = $entityManager->createQuery(
               'SELECT p
            FROM App\Entity\Salle p
            WHERE (p.governorate = :gov ) 
            AND (p.name LIKE :name)
            '
           )
               ->setParameters(array('name' => "%" . $name . "%", 'gov' => $gov));
       }


        return $this->render('salle/showall.html.twig', [
            'salles' => $query->getResult(),
            'users' => $ur->findAll(),
        ]);
    }



    /**
     * @Route("/all", name="allrooms", methods={"GET"})
     */
    public function showall(SalleRepository $sr, UsersRepository $ur): Response
    {           $entityManager = $this->getDoctrine()->getManager();


      //  var_dump($query);
        return $this->render('salle/showall.html.twig', [
            'salles' => $sr->findAll(),
            'users' => $ur->findAll(),

        ]);
    }
    /**
     * @Route("/", name="salle_index", methods={"GET"})
     */
    public function index(SalleRepository $salleRepository): Response
    {
        return $this->render('salle/index.html.twig', [
            'salles' => $salleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="salle_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($salle);
            $entityManager->flush();

            return $this->redirectToRoute('salle_index');
        }

        return $this->render('salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idSalle}", name="salle_show", methods={"GET"})
     */
    public function show(Salle $salle): Response
    {
        return $this->render('salle/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    /**
     * @Route("/{idSalle}/edit", name="salle_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Salle $salle): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('salle_index');
        }

        return $this->render('salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idSalle}", name="salle_delete", methods={"POST"})
     */
    public function delete(Request $request, Salle $salle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getIdSalle(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($salle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('salle_index');
    }
}
